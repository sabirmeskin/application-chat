<?php

namespace App\Livewire\Chat;

use App\Events\MessageDeliveredEvent;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\ConversationService;

class Sidebar extends Component
{

    public $conversations = [];
    public $activeId;

    protected $conversationService;

    public function boot(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    public function mount()
    {
        $this->loadConversations();
    }

    public function loadConversations()
    {
        $this->conversations = $this->conversationService->getConversationsForUser(Auth::user(), false);
    }

    public function getListeners()
    {
        $listeners = [];
        foreach ($this->conversations as $conversation) {
            foreach ($conversation->participants as $key => $value) {
                $listeners["echo-private:conversation,MessageDeliveredEvent'"] = 'handleMessageDelivered';
            }
        }
        $listeners['conversationChanged'] = 'loadConversations';
        $listeners['messageDelivered'] = 'updateSidebar';
        return $listeners;
        // return [
        //     "echo-private:conversation,MessageDeliveredEvent' => 'handleMessageDelivered",
        //     'conversationCreated' => 'loadConversations',
        //     'messageDelivered' => 'updateSidebar',
        // ];
    }
    public function toggleActive($conversationId)
    {
        $this->activeId = $conversationId;
        $conv = Conversation::find($conversationId);
        $conversation = $this->conversationService->getConversationWithMessages($conv, 10);
        $message = $conversation->messages()->latest()->first();
        $this->dispatch('conversationChanged', [
            'conversationId' => $conversationId,
            'conversation' => $conversation,
            'message' => $message,
        ]);
    }

    public function updateSidebar($message)
    {
        if (!$message) {
            return;
        }
        $msg = Message::find($message['message']['message']['id']);
        broadcast(new MessageDeliveredEvent($msg))->toOthers();
    }
    public function handleMessageDelivered($event)
    {
        $message = Message::find($event['message']['id']);
        $user = $message->receiver;
        if ($message->receiver_id === Auth::user()->id) {
            $message->markAsDelivered($user);
            $this->dispatch('Delivered', [
                'message' => $message,
            ]);
        }
        $this->loadConversations();
    }
    public function render()
    {
        return view('livewire.chat.sidebar');
    }
}

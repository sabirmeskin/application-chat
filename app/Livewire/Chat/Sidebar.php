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
    protected $listeners = [];
    public function getListeners()
    {
        return [
            'echo-private:conversation,MessageDeliveredEvent' => 'handleMessageDelivered',
            'conversationCreated' => 'loadConversations',
            'messageDlivered' => 'updateSidebar',
        ];
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
        $msg = Message::find($message['message']['id']);
        broadcast(new MessageDeliveredEvent($msg));
    }
    public function handleMessageDelivered($event)
    {

        // $message = $event['message'];
        // dd($event);
        $message = Message::find($event['message']['id']);
        $user = $message->receiver;
        // dd($user);
        // Check if the message is part of the current conversation
        if ($message->receiver_id === Auth::user()->id) {
            $message->markAsDelivered($user);
            $this->loadConversations();
        }
    }
    public function render()
    {
        return view('livewire.chat.sidebar');
    }
}

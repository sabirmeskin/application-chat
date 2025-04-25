<?php

namespace App\Livewire\Chat;

use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Chatbox extends Component
{
    public $messages = [];
    public $message;
    public $receiver_id;
    public $sender_id;
    public $conversation;
    protected $conversationService;
    protected $messageService;

    public function mount(ConversationService $conversationService, MessageService $messageService)
    {
        $this->conversationService = $conversationService;
        $this->messageService = $messageService;
    }

    public function hydrate()
    {
        $this->conversationService = app(ConversationService::class);
        $this->messageService = app(MessageService::class);
    }
    
    #[On('conversationSelected')]
    public function conversationSelected($conversationId)
    {
        $conversation = $this->conversationService->getConversationWithMessages($conversationId, 8);
        $this->messages = $conversation->messages;
        $receiver = $conversation->participants()
            ->where('user_id', '!=', Auth::id())
            ->first();
        $this->sender_id = Auth::id();
        $this->receiver_id = $receiver->id;
        $this->conversation = $conversation;
    }

    public function sendMessage(MessageService $messageService)
    {
        if (trim($this->message) === '') {
            return;
        }

        $newMessage = $messageService->sendTextMessage(
            Auth::user(),
            $this->conversation,
            null,
            $this->message
        );
        $this->messages[] = $newMessage;
        $this->message = '';
        $this->dispatch('messageSent', $newMessage);
        dump($this->getListeners());
    }

    public function getListeners()
    {
        $listeners = [];
        if ($this->conversation) {
            $listeners["echo-private:chat.{$this->conversation->id},MessageSentEvent"] = 'handleMessageSentEvent';
        }
        return $listeners;
    }

    public function handleMessageSentEvent($event)
    {
        $msg = $this->messageService->getMessageById($event['message']['id']);
        $this->messages[] = $msg;
    }

    /**
     * Get the previous message for a given message index
     */
    public function getPreviousMessage($index)
    {
        if ($index > 0) {
            return $this->messages[$index - 1];
        }
        return null;
    }

    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}
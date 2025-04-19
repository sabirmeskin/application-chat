<?php

namespace App\Livewire\Chat;

use App\Events\MessageDeliveredEvent;
use App\Events\MessageSentEvent;
use App\Events\TestEvent;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chatbox extends Component
{
    use WithFileUploads;
    public $conversation;
    public $messages = [];
    public $message = '';
    public $files = [];
    public $isTyping = false;
    public $typingUser = null;
    protected $conversationService;
    protected $messageService;



    public function boot(ConversationService $conversationService, MessageService $messageService)
    {
        $this->conversationService = $conversationService;
        $this->messageService = $messageService;
    }
    public function mount($conversation = null)
    {
        if ($conversation) {
            $this->loadConversation($conversation);
        }
    }
    public function getListeners()
    {
        return [
            'conversationChanged' => 'loadConversation',
            'echo-private:conversation,MessageSentEvent' => 'handleIncomingMessage',
        ];
    }
    public function loadConversation($data)
    {

        $conv = Conversation::find($data['conversationId']);
        $msg = Message::find($data['message']['id']);
        if ($msg->sender_id !== Auth::user()->id) {
        }
        $this->conversation = $this->conversationService->getConversationWithMessages($conv, 10);
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required_without:files|string|max:1000',
            'files.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        $message = $this->messageService->sendTextMessage(
            Auth::user(),
            $this->conversation,
            null,
            $this->message
        );

        // Handle file uploads if any
        if ($this->files) {
            foreach ($this->files as $file) {
                $message->addMedia($file)->toMediaCollection('chat');
            }
        }

        $this->messages[] = $message;
        $this->reset(['message', 'files']);
        $this->dispatch('messageSent', [
            'message' => $message,
        ]);
        // Broadcast the new message to other participants
        broadcast(new MessageSentEvent($message));
    }

    public function handleIncomingMessage($event)
    {
        $message = $event['message'];

        // Check if the message is part of the current conversation
        if ($this->conversation && $this->conversation->id === $message['conversation_id']) {
            $this->messages[] = $message;
        }
    }




    public function deleteConversation()
    {
        $this->conversation->delete();
        $this->dispatch('conversationDeleted');
        $this->redirect(route('chat'));
    }

    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}

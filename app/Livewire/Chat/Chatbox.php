<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chatbox extends Component
{
    public $conversation;
    public $messages = [];
    public $message = '';
    public $files = [];
    public $isTyping = false;
    public $typingUser = null;
    protected $conversationService;
    protected $messageService;
    protected $listeners = [
        'conversationChanged' => 'loadConversation',
        'userTyping' => 'showTypingIndicator',
        'userStoppedTyping' => 'hideTypingIndicator'
    ];


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

    public function loadConversation($data)
    {
        $conv = Conversation::find($data['conversationId']);
        $this->conversation = $this->conversationService->getConversationWithMessages($conv, 10);
        return $this->conversation;
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
                $path = $file->store('message_attachments', 'public');
                $message->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        $this->messages[] = $message;
        $this->reset(['message', 'files']);

        // Broadcast the new message to other participants
        $this->dispatch('messageSent', messageId: $message->id);
    }

    public function showTypingIndicator($userId)
    {
        if ($userId != Auth::id()) {
            $this->isTyping = true;
            $this->typingUser = User::find($userId);
        }
    }

    public function hideTypingIndicator($userId)
    {
        if ($userId != Auth::id()) {
            $this->isTyping = false;
            $this->typingUser = null;
        }
    }

    public function archiveConversation()
    {
        $service = app(ConversationService::class);
        $service->archiveConversation(Auth::user(), $this->conversation);
        $this->dispatch('conversationArchived');
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

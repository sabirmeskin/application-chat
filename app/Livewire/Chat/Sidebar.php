<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
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
    protected $listeners = [
        'conversationCreated' => 'loadConversations'
    ];

    public function toggleActive($conversationId)
    {
        $this->activeId = $conversationId;
        $conv = Conversation::find($conversationId);
        $conversation = $this->conversationService->getConversationWithMessages($conv, 10);
        $this->dispatch('conversationChanged', [
            'conversationId' => $conversationId,
            'conversation' => $conversation,
        ]);
    }

    public function render()
    {
        return view('livewire.chat.sidebar');
    }
}

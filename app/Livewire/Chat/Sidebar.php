<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\ConversationService;

class Sidebar extends Component
{

    public $conversations = [];
    public $activeId ;

    protected $conversationService;

    public function mount(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
        $this->loadConversations();

    }

    public function loadConversations()
    {
        $this->conversations = $this->conversationService->getConversationsForUser(Auth::user(), false);

    }

    public function toggleActive($conversationId)
    {
        $this->activeId = $conversationId;
    }

    public function render()
    {
        return view('livewire.chat.sidebar');
    }
}

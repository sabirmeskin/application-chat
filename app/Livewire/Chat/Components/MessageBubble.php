<?php

namespace App\Livewire\Chat\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MessageBubble extends Component
{
    public $message;
    public $userId;
    public $avatarOn;
    // public $isRead;
    public function mount($message,$avatarOn)
    {
        $this->avatarOn = $avatarOn;
        $this->message = $message;
        // $this->isRead = $isRead;
        $this->userId = Auth::id();
    }
    public function render()
    {
        return view('livewire.chat.components.message-bubble');
    }
}

<?php

namespace App\Livewire\Chat\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MessageBubble extends Component
{
    public $message;
    public $userId;

    public function mount($message)
    {
        $this->message = $message;
        $this->userId = Auth::id();
    }
    public function render()
    {
        return view('livewire.chat.components.message-bubble');
    }
}

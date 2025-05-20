<?php

namespace App\Livewire\Chat\Components;

use App\Events\MessageDeletedEvent;
use App\Events\MessageReadEvent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MessageBubble extends Component
{
    public $message;
    public $userId;
    public $avatarOn;
    // public $isRead;
    public function delete(){
        $this->dispatch('messageDeleted', $this->message);
        broadcast(new MessageDeletedEvent($this->message))->toOthers();
    }

    public function mount($message,$avatarOn)
    {
        $this->avatarOn = $avatarOn;
        $this->message = $message;
        $this->userId = Auth::id();
    }
    public function render()
    {
        return view('livewire.chat.components.message-bubble');
    }
}

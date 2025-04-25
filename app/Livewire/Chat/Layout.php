<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Livewire\Attributes\On;
use Livewire\Component;

class Layout extends Component
{
    public $conversation;
    #[On('conversationSelected')]
    public function conversationSelected($conversationId)
    {
        // dd('chatLayout');
        $this->conversation = Conversation::find($conversationId);

    }


    public function render()
    {
        return view('livewire.chat.layout');
    }
}
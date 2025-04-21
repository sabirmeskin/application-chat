<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Livewire\Component;

class Chatbox extends Component
{
    public $messages = [];
    public $message = '';
    public $conversation;

    public function mount()
    {
        $this->conversation = Conversation::find(13);
        $this->messages = $this->conversation->messages()->with('user')->get();
    }
    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}

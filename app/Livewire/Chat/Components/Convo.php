<?php

namespace App\Livewire\Chat\Components;

use App\Models\Conversation;
use Livewire\Component;

class Convo extends Component
{
    public $conversation;

    public function mount($conversation)
    {
        $this->conversation = $conversation;
    }


    public function render()
    {
        return view('livewire.chat.components.convo');
    }
}

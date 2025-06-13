<?php

namespace App\Livewire\Chat\Components\Features;

use App\Models\Message;
use Livewire\Component;

class Reply extends Component
{
    public Message $message;
    public $message_id;
    public function mount($message_id)
    {
        $this->message = Message::find($message_id);
    }

    public function render()
    {
        return view('livewire.chat.components.features.reply');
    }
}

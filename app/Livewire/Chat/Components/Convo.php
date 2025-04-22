<?php

namespace App\Livewire\Chat\Components;

use App\Models\Conversation;
use Livewire\Component;

class Convo extends Component
{
    public $conversation;

    public function getListeners()
    {
        return [
            "echo-private:chat,MessageSentEvent" => 'updateconversationForReceiver',
            'messageSent' => 'updateconversationForSender',
        ];
    }
    public function mount($conversation)
    {
        $this->conversation = $conversation;
    }
    public function updateconversationForSender($event)
    {
        // dd($event);
        $this->conversation = Conversation::find($event['conversation_id']);
    }
    public function updateconversationForReceiver($event)
    {
        // dd($event['message']['conversation_id']);
        $this->conversation = Conversation::find($event['message']['conversation_id']);
    }

    public function render()
    {
        return view('livewire.chat.components.convo');
    }
}

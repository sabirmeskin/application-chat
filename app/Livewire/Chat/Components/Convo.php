<?php

namespace App\Livewire\Chat\Components;

use Livewire\Component;

class Convo extends Component
{
    public $conversation;

    public function mount($conversation)
    {
        $this->conversation = $conversation;
    }
    public function getListeners()
    {
        return [
            'messageSent' => 'updateConversations',

        ];
    }
    public function updateConversations($message)
    {
        if (!$message) {
            return;
        }
        $this->dispatch('messageDelivered', [
            'message' => $message,
        ]);
    }
    public function render()
    {
        return view('livewire.chat.components.convo');
    }
}

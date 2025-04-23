<?php

namespace App\Livewire\Chat\Components;

use App\Models\Conversation;
use Livewire\Component;

class Convo extends Component
{
    public $conversation;

    public function getListeners()
    {
        $listeners = [
            'messageSent' => 'updateconversationForSender',
            'echo-private:user.active,user.active' => 'handleUserActive',
        ];
        if ($this->conversation) {
            $listeners["echo-private:chat.{$this->conversation->id},MessageSentEvent"] = 'updateconversationForReceiver';
        }
        return $listeners;
    }
    public function mount($conversation)
    {
        $this->conversation = $conversation;
    }
    public function handleUserActive($event)
    {
        // dd($event);
    }
    public function updateconversationForSender($event)
    {
        $this->conversation->load('lastMessage');
    }
    public function updateconversationForReceiver($event)
    {
        $this->conversation = Conversation::find($event['message']['conversation_id']);
    }

    public function render()
    {
        return view('livewire.chat.components.convo');
    }
}

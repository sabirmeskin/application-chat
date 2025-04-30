<?php

namespace App\Livewire\Chat\Components;
use Livewire\Attributes\On;
use App\Models\Conversation;
use App\Services\ConversationService;
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
            "echo-private:chat.{$this->conversation->id},MessageSentEvent" => 'UpdateLastMessage',
            'echo:private-conversation,ConversationUpdatedEvent' => 'handleUpdateConversationEvent',

         ];
    }
    public function handleUpdateConversationEvent(){
        $this->conversation->load('participants');
    }


    public function UpdateLastMessage()
    {
        $this->conversation->load('lastMessage');
    }

    public function render()
    {
        return view('livewire.chat.components.convo');
    }
}

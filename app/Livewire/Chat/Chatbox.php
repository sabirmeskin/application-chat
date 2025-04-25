<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;


class Chatbox extends Component
{
    public $messages = [];
    public $message = '';
    public $conversation;
    protected $messageService;



    public function sendMessage(MessageService $messageService)
    {
        if (trim($this->message) === '') {
            return;
        }

       $newMessage =  $messageService->sendTextMessage(
            Auth::user(),
            $this->conversation,
            null,
            $this->message
        );
        $this->messages [] = $newMessage;
        $this->dispatch('messageSent', $newMessage);
        $this->message = '';

    }
    public function loadMessages(){
       $this->messages[] = $this->conversation->messages;
    }

    public function getListeners()
    {
        return ["echo-private:chat.{$this->conversation->id},MessageSentEvent" => 'UpdateLastMessage'];
    }

    public function UpdateLastMessage(){

    }
    public function hydrate()
    {

    }
    public function mount($conversation)
    {

        $this->conversation = $conversation;
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}

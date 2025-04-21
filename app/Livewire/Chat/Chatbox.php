<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;


class Chatbox extends Component
{
    public $messages = [];
    public $message = '';
    public $conversation;

    #[On('conversationSelected')]
    public function conversationSelected($conversationId)
    {
        $this->conversation = Conversation::find($conversationId);
        $this->messages = $this->conversation->messages;
    }

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
        $this->message = '';
    }

    public function getListeners()
    {
        if (!$this->conversation) {
            return [];
        }
        return [
            "echo-private:chat.{$this->conversation->id},MessageSentEvent" => 'test',
        ];
    }
    public function test($event)
    {
       dd($event) ;
    }

    public function mount()
    {

    }
    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}

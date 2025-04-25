<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
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
        $this->dispatch('scrollToBottom');
        $this->message = '';

    }
    public function loadMessages(){
        $this->messages = $this->conversation
        ->messages()
        ->oldest()
        ->get();

    $this->dispatch('scrollToBottom');
    }

    public function getListeners()
    {
        return ["echo:private-chat.{$this->conversation->id},MessageSentEvent" => 'updateLastMessage'];
    }

    public function updateLastMessage($event){
    // Create a new Message model from the array
    $newMessage = Message::find($event['message']['id']);

    // Add the new message to the messages array
    $this->messages[] = $newMessage;
    $this->dispatch('scrollToBottom');
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

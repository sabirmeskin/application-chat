<?php

namespace App\Livewire\Chat;

use App\Events\MessageReadEvent;
use App\Events\MessageSentEvent;
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
    public $isRead = false;


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

        $this->dispatch('messageSent', [$this->conversation,$newMessage]);
        $this->dispatch('scrollToBottom');
        broadcast(new MessageReadEvent($newMessage , Auth::id()))->toOthers();
        $this->message = '';

    }
    public function loadMessages(){
      $message =  $this->messages = $this->conversation
        ->messages()
        ->oldest()
        ->get();

        $this->dispatch('scrollToBottom');
        $lastmessage =   $message->last();
        broadcast(new MessageReadEvent($lastmessage , Auth::id()))->toOthers();

    }

    public function getListeners()
    {
        return [
            "echo:private-chat.{$this->conversation->id},MessageSentEvent" => 'updateLastMessage',
            "echo:private-read.{$this->conversation->id},MessageReadEvent" => 'handleMessageRead'
    ];
    }

    public function handleMessageRead($event){
        $msg = Message::findorFail($event['message_id']);
        if($msg){
            $msg->markAsRead(Auth::user());
            $this->isRead = true;
        }

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

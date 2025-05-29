<?php

namespace App\Livewire\Chat;

use App\Events\MessageReadEvent;

use App\Events\TypingEvent;

use App\Models\Message;
use App\Models\User;

use App\Services\MessageService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chatbox extends Component
{
     use WithFileUploads;
     public $file;
    public $messages = [];
    public $message = '';
    public $conversation;
    protected $messageService;
    public $isRead = false;
    public $typingIndicator = false;
    public $messageToDelete = null;
    public $onlineUsers = [];



    public function updatedFile()
    {
        $this->sendFileMessage();
    }

    public function sendFileMessage(){
        if (!$this->file) return;
        $message = MessageService::getInstance()->sendMediaMessage(
            Auth::user(),
            $this->conversation,
        );
        $message->addMedia($this->file->getRealPath())
            ->usingFileName($this->file->getClientOriginalName())
            ->toMediaCollection('attachments');

              $this->reset('file');

        $this->dispatch('messageSent', [$this->conversation, $message]);
        $this->dispatch('scrollToBottom');
    }

    public function sendMessage(MessageService $messageService)
    {
        $messageText = $this->message;
        $this->message = '';
        $this->stopTyping();
        if (trim($messageText) === '') {
            return;
        }

       $newMessage =  $messageService->sendTextMessage(
            Auth::user(),
            $this->conversation,
            null,
            $messageText
        );
        // $this->messages[] = $newMessage;

        $this->dispatch('messageSent', [$this->conversation,$newMessage]);
        $this->dispatch('scrollToBottom');
        // broadcast(new MessageReadEvent($newMessage , Auth::id()))->toOthers();

    }
    public function loadMessages(){
     $this->messages = $this->conversation
        ->messages()
        ->latest()
        ->take(10)
        ->get()->reverse();

        $this->dispatch('scrollToBottom');

        // broadcast(new MessageReadEvent($lastmessage , Auth::id()))->toOthers();

    }



    public function handleTypingEvent($event){
        $this->typingIndicator = true;
        $this->dispatch('hideTypingAfterDelay');
    }

    public function handleMessageRead($event)
    {
        $messageId = $event['message_id'];
        $userId = $event['reader_id']; // ID of the user who read it

        // Find the index of the message in the current messages array
        $index = $this->messages->search(fn($m) => $m->id == $messageId);

        if ($index !== false) {


            $message = Message::find($messageId);

            $message->markAsRead(User::find($userId)); // Make sure you're passing the right user
            $this->messages[$index] = $message->fresh(); //
        }
    }

    public function updateLastMessage($event){
    // Create a new Message model from the array
    $newMessage = Message::find($event['message']['id']);

    // Add the new message to the messages array
    $this->messages[] = $newMessage;
    $this->dispatch('scrollToBottom');
    }
    public function markLastMessageAsSeen($messageId)
    {
        $message= Message::find($messageId);
        if($message && $message->sender_id !== Auth::id()){
            broadcast(new MessageReadEvent($message, Auth::id()))->toOthers();
        }
    }

    public function startTyping(){

        broadcast(new TypingEvent($this->conversation ))->toOthers();
    }
    public function stopTyping(){

        $this->typingIndicator =  false;
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

    public function getVisibleMessagesProperty()
    {
        return collect($this->messages)
            ->filter(fn($m) => is_null($m->deleted_at))
            ->values();
    }


    // #[On('messageDeleted')]
  public function handleMessageDeleted($event)
    {
        $messageId = $event['message']['id'] ?? null;

        if (! $messageId) {
            return;
        }

        // Set deleted_at on the matching message
        // $this->messages = collect($this->messages)->map(function ($m) use ($messageId) {
        //     if ($m->id === $messageId) {
        //         $m->deleted_at = now(); // simulate deletion
        //     }
        //     return $m;
        // })->values();
       $this->messages
            ->filter(fn ($msg) => $msg->id !== $messageId)
            ->values();


        $this->dispatch('messageDeleted', $messageId);
    }

    public function messageDeleted($event)
    {
        $messageId = $event['id'] ?? null;
        if (! $messageId) {
            return;
        }

        $this->messages
        ->filter(fn ($msg) => $msg->id !== $messageId)
        ->values();
    }
    
    public function editMessage($messageId)
    {
        if (! $messageId) {
            return;
        }
        $message = Message::find($messageId);
        $this->message = $message->body;
        $message->update([
            'edited_at' => now(),     

        ]);
        //  dd($message->body);
    }

  

    public function userListUpdated(array $users)
    {
        $this->onlineUsers = $users;
    }
    
    public function userJoined(array $user)
    {
        if (!collect($this->onlineUsers)->pluck('id')->contains($user['id'])) {
            $this->onlineUsers[] = $user;
        }
        $this->dispatch('userStatusOnLine', $user);
    }
    
    public function userLeft(array $user)
    {
        $this->onlineUsers = collect($this->onlineUsers)
            ->reject(fn($u) => $u['id'] === $user['id'])
            ->values()
            ->toArray();
    }
        public function getListeners()
    {
        return [
            "echo:private-chat.{$this->conversation->id},MessageSentEvent" => 'updateLastMessage',
            "echo:private-read.{$this->conversation->id},MessageReadEvent" => 'handleMessageRead',
            "echo:private-typing.{$this->conversation->id},TypingEvent" => 'handleTypingEvent',
            "echo:private-message,MessageDeletedEvent" => 'handleMessageEdited',
            "echo:private-message,MessageEditedEvent" => 'handleMessageDeleted',
            "messageDeleted" => 'messageDeleted',
            "editMessage" => 'editMessage',
             'echo-presence:user-status,here' => 'userListUpdated',
            'echo-presence:user-status,joining' => 'userJoined',
            'echo-presence:user-status,leaving' => 'userLeft',
    ];
    }
}
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



    // #[On('messageDeleted')]
    public function onMessageDeleted($messageData)
    {
        $messageId = $messageData['message']['id'] ?? null;

        if (! $messageId) {
            return;
        }

        // Find and delete from DB
        $message = Message::find($messageId);

        if ($message) {
            $message->delete();

            // Remove it from the messages collection in memory
            $this->messages = $this->messages
                ->filter(fn ($msg) => $msg->id !== $message->id)
                ->values(); // <- important to reindex after filtering
        }

        $this->dispatch('scrollToBottom');
    }



        public function getListeners()
    {
        return [
            "echo:private-chat.{$this->conversation->id},MessageSentEvent" => 'updateLastMessage',
            "echo:private-read.{$this->conversation->id},MessageReadEvent" => 'handleMessageRead',
            "echo:private-typing.{$this->conversation->id},TypingEvent" => 'handleTypingEvent',
            "echo:private-message,MessageDeletedEvent" => 'onMessageDeleted',
    ];
    }
}

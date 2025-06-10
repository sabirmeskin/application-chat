<?php

namespace App\Livewire\Chat;

use App\Events\MessageReadEvent;
use App\Events\MessageReplyEvent;
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
    public $parent = null;
    public $reply = false;




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
            $this->parent ? Message::find($this->parent) : null,
            $messageText
        );
        // dd($newMessage);
        // $this->messages[] = $newMessage;
        
        $this->dispatch('messageSent', [$this->conversation,$newMessage]);
        if ($this->parent) {
            broadcast(new MessageReplyEvent($newMessage, $this->parent))->toOthers();
        }
        $this->message = ''; // Clear the message input after sending
        $this->parent ? $this->dispatch('sendReply') : ''; // Reset parent after sending the message
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
        // dd($this->messages);
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
            // If the message is found, mark it as read
            // and update the message in the array


            $message = Message::find($messageId);

            $message->markAsRead(User::find($userId)); // Make sure you're passing the right user


            $this->dispatch('messageReadRefresh');

        }
    }

    public function updateLastMessage($event){
    // Create a new Message model from the array
    $newMessage = Message::find($event['message']['id']);

    // Add the new message to the messages array
    $this->messages[] = $newMessage;
    $this->dispatch('scrollToBottom');
    }
    /**
     * Mark the last message as seen.
     *
     * @param int $messageId
     * @return void
     */
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
    public function onMessageForwarded($payload)
    {
        // dd($payload['message']);
        $message = Message::find($payload['message']['id']);

        if ($message && $message->conversation_id === $this->conversation->id) {
            // Only add the message if it belongs to the current conversation
            $this->messages->push($message);
        }
    }

  public function replyToMessage($messageId)
    {
        $message = Message::find($messageId);
        if ($message) {
            $this->parent = $message->id;
        }
    }

        public function getListeners()
{
        $userId = Auth::id();
    return [
        // New message sent in this conversation
        "echo-private:chat.{$this->conversation->id},MessageSentEvent"        => 'updateLastMessage',

        // Someone read a message in this conversation
        "echo-private:read.{$this->conversation->id},MessageReadEvent"        => 'handleMessageRead',

        // Typing indicator in this conversation
        "echo-private:typing.{$this->conversation->id},TypingEvent"             => 'handleTypingEvent',

        // A message was deleted in this conversation
        "echo-private:message,MessageDeletedEvent"     => 'handleMessageDeleted',

        // A message was edited in this conversation
        "echo-private:chat.{$this->conversation->id},MessageEditedEvent"      => 'handleMessageEdited',

        // Forwarded messages arrive on *your* user channel
        "echo-private:message,MessageForwardedEvent"         => 'onMessageForwarded',

        // Livewire-dispatched events (e.g. from modal)
        'messageDeleted'                                                     => 'messageDeleted',
        'editMessage'                                                        => 'editMessage',

        // Presence (online users) — adjust channel name if yours differs
        'echo-presence:user-status,here'                                     => 'userListUpdated',
        'echo-presence:user-status,joining'                                  => 'userJoined',
        'echo-presence:user-status,leaving'                                  => 'userLeft',
        'echo-presence:user-status,leaving'                                  => 'userLeft',
        'replyToMessage'                                  => 'replyToMessage',
    ];
}

}

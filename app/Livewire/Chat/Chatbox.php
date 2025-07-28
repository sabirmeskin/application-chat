<?php

namespace App\Livewire\Chat;

use App\Events\MessageEditedEvent;
use App\Events\MessageReadEvent;
use App\Events\MessageReplyEvent;
use App\Events\TypingEvent;
use App\Events\UserRejoinedConversationEvent;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\ConversationUserLog;
use App\Models\Message;
use App\Models\User;

use App\Services\MessageService;

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
    public $replyBox = false;
    public $replyTo;
    public $editMode = false;
    public $editMessageId = null;

    public function updatedFile()
    {
        $this->sendFileMessage();
    }

    public function sendFileMessage()
    {
        if (!$this->file) return;
        $message = MessageService::getInstance()->sendMediaMessage(
            Auth::user(),

            $this->conversation,
            $this->replyTo
        );
        $message->addMedia($this->file->getRealPath())
            ->usingFileName($this->file->getClientOriginalName())
            ->toMediaCollection('attachments');

        $this->reset('file');
        // $this->replyTo = null; // Reset reply after sending
        $this->replyBox = false; // Hide reply box after sending



        $this->dispatch('messageSent', [$this->conversation, $message]);
        $this->dispatch('scrollToBottom');
    }


    // public function sendMessage(MessageService $messageService)
    // {
    //     $messageText = $this->message;
    //     $this->message = '';
    //     $this->stopTyping();

    //     if (trim($messageText) === '') {
    //         return;
    //     }
    //       $participant = $this->conversation->participants()
    //         ->where('user_id',Auth::user()->id)
    //         ->first();
    //     if ($participant) {   
    //     foreach ($this->conversation->participants as $participant) {
    //         if ($participant->id !== Auth::id()) {
    //             $this->conversation->participants()->updateExistingPivot($participant->id, [
    //                 'deleted_at' => null
    //             ]);
    //         }
    //     }

    // }
    //     $parentMessageId = $this->replyTo; // Get the ID of the message being replied to
    //     $newMessage =  $messageService->sendTextMessage(
    //         Auth::user(),
    //         $this->conversation,
    //         $parentMessageId,
    //         $messageText
    //     );
    //     $this->replyBox = false; // Hide reply box after sending
    //     $this->dispatch('messageSent', [$this->conversation, $newMessage]);
    //     $this->dispatch('scrollToBottom');


    // }
    public function sendMessage(MessageService $messageService)
    {
        $messageText = $this->message;
        $this->message = '';
        $this->stopTyping();

        if (trim($messageText) === '') {
            return;
        }

        $participants = $this->conversation
            ->participants()
            ->withPivot('deleted_at')
            ->get();

        foreach ($participants as $participant) {
            if ($participant->pivot->deleted_at !== null) {
                // dd("Participant {$participant->id} is soft-deleted, restoring...");
                $this->conversation->participants()->updateExistingPivot($participant->id, [
                    'deleted_at' => null,
                ]);

                ConversationUserLog::create([
                    'conversation_id' => $this->conversation->id,
                    'user_id' => $participant->id,
                    'action' => 'rejoined',
                    'action_at' => now(),
                    'note' => 'Automatically rejoined upon receiving new message',
                ]);
                broadcast(new UserRejoinedConversationEvent($this->conversation, $participant->id))->toOthers();
            }
        }


        $parentMessageId = $this->replyTo;

        $newMessage = $messageService->sendTextMessage(
            Auth::user(),
            $this->conversation,
            $parentMessageId,
            $messageText
        );

        $this->replyBox = false;
        $this->dispatch('messageSent', [$this->conversation, $newMessage]);
        $this->dispatch('scrollToBottom');
    }
    //    public function loadMessages()
    // {
    //     $participant = $this->conversation->participants()
    //         ->where('user_id', Auth::id())
    //         ->whereNull('deleted_at')
    //         ->first();

    //     if (! $participant) {
    //         $this->messages = collect(); // Return an empty collection
    //         return;
    //     }

    //     $this->messages = $this->conversation
    //         ->messages()
    //         ->latest()
    //         ->take(10)
    //         ->get()
    //         ->reverse();

    //     $this->dispatch('scrollToBottom');
    // }

    public function loadMessages()
    {
        $user = Auth::user();

        $lastAction = \App\Models\ConversationUserLog::where('conversation_id', $this->conversation->id)
            ->where('user_id', $user->id)
            ->whereIn('action', ['deleted', 'rejoined'])
            ->latest('action_at')
            ->first();

        $startTime = $lastAction?->action_at;

        $this->messages = $this->conversation->messages()
            ->when($startTime, fn($q) => $q->where('created_at', '>=', $startTime))
            ->latest('created_at')
            ->take(50)
            ->get()
            ->reverse();
    }




    public function handleTypingEvent($event)
    {
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

    public function updateLastMessage($event)
    {
        // Create a new Message model from the array
        $newMessage = Message::find($event['message']['id']);

        // Add the new message to the messages array
        $this->messages[] = $newMessage;
        $this->dispatch('scrollToBottom');
        if ($this->replyTo) {
            $this->dispatch('cancelReply');
        }
    }
    /**
     * Mark the last message as seen.
     *
     * @param int $messageId
     * @return void
     */
    public function markLastMessageAsSeen($messageId)
    {
        $message = Message::find($messageId);
        if ($message && $message->sender_id !== Auth::id()) {
            broadcast(new MessageReadEvent($message, Auth::id()))->toOthers();
        }
    }

    public function startTyping()
    {

        broadcast(new TypingEvent($this->conversation))->toOthers();
    }
    public function stopTyping()
    {

        $this->typingIndicator =  false;
    }
    public function mount($conversation)
    {
        $user = Auth::user();

        $participant = $conversation->participants()
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if (! $participant) {
            abort(403, 'Unauthorized or conversation deleted.');
        }

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
            ->filter(fn($msg) => $msg->id !== $messageId)
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
            ->filter(fn($msg) => $msg->id !== $messageId)
            ->values();
    }

    public function editMessage($messageId)
    {
        if (! $messageId) {
            return;
        }
        $message = Message::find($messageId);
        if (! $message) {
            return;
        }
        $this->editMessageId = $messageId;
        $this->message = $message->body;
        $this->editMode = true;
    }
    public function cancelEdit()
    {
        $this->message = '';
        $this->editMode = false;

        $this->replyTo = null; // Reset reply after canceling edit
    }

    public function updateMessage($messageId)
    {

        if (!$messageId || !$this->message) {
            return;
        }
        if (trim($this->message) === '') {
            return;
        }
        $message = Message::find($messageId);
        if (!$message) {
            return;
        }
        $this->messageService = MessageService::getInstance();
        $message = $this->messageService->editMessage($message, $this->message);
        // Update the message in the messages array
        $this->dispatch('messageEdited', $message);
        broadcast(new MessageEditedEvent($message))->toOthers();
        $this->message = '';
        $this->editMessageId = null;
        $this->editMode = false;
        $this->replyTo = null;
    }

    public function handleMessageEdited($event)
    {
        $message = $event['message'];
        $messageId = $message['id'] ?? null;

        if (!$messageId) {
            return;
        }

        // Find the message in the current messages array
        $index = $this->messages->search(fn($m) => $m->id == $messageId);

        if ($index !== false) {
            // If the message is found, update it
            $this->messages[$index] = Message::find($messageId);
            $this->dispatch('messageEdited', $this->messages[$index]);
        }
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
    public function setReplyTo($messageId)
    {
        if (!$messageId) {
            $this->replyTo = null;
            return;
        }
        $this->replyBox = true;

        $this->replyTo = Message::find($messageId)->id;
    }

    public function cancelReply()
    {
        $this->replyBox = false;
        $this->replyTo = null;
    }
    #[On('deleteConversation')]
    public function deleteConversation($conversationId)
    {
        $user = Auth::user();
        $conversation = Conversation::find($conversationId);
        if (!$conversation) return;

        $participant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->first();

        if ($participant) {
            $participant->deleted_at = now();
            $participant->save();

            // Log the delete action
            ConversationUserLog::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'action' => 'deleted',
                'action_at' => now(),
                'note' => 'User deleted the conversation manually',
            ]);
        }

        $this->dispatch('conversationDeleted', $conversationId);
    }




    //     public function deleteConversation($conversationId)
    // {
    //     $user = Auth::user();
    //     $conversation = Conversation::find($conversationId);
    //     if (!$conversation) return;

    //     $participant = ConversationParticipant::where('conversation_id', $conversation->id)
    //         ->where('user_id', $user->id)
    //         ->first();

    //     if ($participant) {
    //         $participant->deleted_at = now();
    //         $participant->save();
    //     }


    //     $this->dispatch('conversationDeleted', $conversationId); // optional frontend action
    //     $this->dispatch('redirect-to-sidebar');

    // }
    public function getListeners()
    {
        $userId = Auth::id();
        return [
            // New message sent in this conversation
            "echo-private:chat.{$this->conversation->id},MessageSentEvent"        => 'updateLastMessage',

            // Someone read a message in this conversation
            "echo-private:read.{$this->conversation->id},MessageReadEvent"        => 'handleMessageRead',

            // Typing indicator in this conversation
            "echo-private:typing.{$this->conversation->id},TypingEvent"           => 'handleTypingEvent',

            // A message was deleted in this conversation
            "echo-private:message,MessageDeletedEvent"                            => 'handleMessageDeleted',

            // A message was edited in this conversation
            "echo-private:EditMessage.{$this->conversation->id},MessageEditedEvent"      => 'handleMessageEdited',

            // Forwarded messages arrive on *your* user channel
            "echo-private:message,MessageForwardedEvent"                         => 'onMessageForwarded',

            // Livewire-dispatched events (e.g. from modal)
            'messageDeleted'                                                     => 'messageDeleted',
            'editMessage'                                                        => 'editMessage',

            // Presence (online users) — adjust channel name if yours differs
            'echo-presence:user-status,here'                                     => 'userListUpdated',
            'echo-presence:user-status,joining'                                  => 'userJoined',
            'echo-presence:user-status,leaving'                                  => 'userLeft',
            'replyMessage'                                                       => 'setReplyTo',
            'cancelReply'                                                        => 'cancelReply',


        ];
    }
}

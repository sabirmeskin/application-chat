<?php
namespace App\Services;

use App\Events\MessageSentEvent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\App;

class MessageService
{


        /**
     * Get the instance of the ConversationService.
     * hadi singleton
     * @return self
     */
    public static function getInstance(): self
    {
        return App::make(self::class);
    }

    public function __construct()
    {
        // Initialize any dependencies or properties here
    }

    /**
     * Send a text message.
     *
     * @param User $sender
     * @param Conversation $conversation
     * @param Message|null $parent
     * @param string $body
     * @return Message
     */
    public function sendTextMessage(User $sender, Conversation $conversation, ?int $parent , string $body):Message
    {

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $conversation->receiver()->id,
            'parent_id' => $parent ? $parent : null,
            'type' => 'text',
            'body' => $body,
        ]);
        broadcast(new MessageSentEvent($message));
        return $message;
    }

    public function sendMediaMessage(User $sender, Conversation $conversation, ?int $parent  ):Message
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $conversation->receiver()->id,
            'parent_id' => $parent ? $parent : null,
            'type' => 'media',
            'body' => '',

        ]);
        broadcast(new MessageSentEvent($message));
        return $message;
    }

    public function editMessage(Message $message, string $body):Message
    {
        $message->update([
            'body' => $body,
        ]);
        return $message;
    }

    public function deleteMessage(Message $message):void

    {
        $message->update([
            'deleted_at' => now(),
        ]);
    }



    // public function addReaction(Message $message, string $reaction):Message
    // {
    //     $message->update([
    //         'reaction' => $reaction,
    //     ]);
    //     return $message;
    // }
    // public function removeReaction(Message $message):Message
    // {
    //     $message->update([
    //         'reaction' => null,
    //     ]);
    //     return $message;
    // }
}

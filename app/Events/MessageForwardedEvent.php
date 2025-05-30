<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class MessageForwardedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Message $message)
    {
         $this->message = $message;
    }

     public function broadcastOn()
    {
        return new PrivateChannel('message' );
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id'            => $this->message->id,
                'body'          => $this->message->body,
                'conversation_id' => $this->message->conversation_id,
                'sender'        => [
                    'id'   => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                ],
            ]
        ];
    }
}

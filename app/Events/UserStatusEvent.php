<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user, public string $status)
    {
        $this->user = $user;
        $this->status = $status;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // new PrivateChannel('user-status.' . $this->user->id),
            new PresenceChannel('user-status'),

        ];
    }
 
    public function broadcastWith(): array
{
    return [
        'id' => $this->user->id,
        'name' => $this->user->name,
        'status' => $this->status,
    ];
}

public function broadcastAs(): string
{
    return $this->status === 'online' ? 'UserOnline' : 'UserOffline';
}
}

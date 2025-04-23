<?php

namespace App\Models;

use App\Events\UserStatusEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use InteractsWithMedia;

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_seen_at',
        'is_online',
        'is_active_in_conversation',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'sender_id')->latest();
    }
    
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class,'conversation_participants')->withTimestamps();
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function archivedConversations()
    {
        return $this->hasMany(ArchivedConversation::class);
    }

    public function markAsOnline()
    {
        $this->update(['is_online' => true]);
        broadcast(new UserStatusEvent($this, true))->toOthers();
    }

    public function markAsOffline()
    {
        $this->update(['is_online' => false, 'last_seen_at' => now()]);
        broadcast(new UserStatusEvent($this, true))->toOthers();

    }

    public function updateLastSeen()
    {
        $this->update(['last_seen_at' => now()]);
    }

    public function isActiveInConversation($conversationId)
    {
        return $this->conversations()->where('conversation_id', $conversationId)->exists();
    }
    public function markAsInactiveInConversation($conversationId)
    {
        $this->is_activce_in_conversation = false;
        $this->conversations()->detach($conversationId);
    }
    public function markAsActiveInConversation($conversationId)
    {
        $this->is_activce_in_conversation = true;

    }


}

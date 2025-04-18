<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    protected $guarded = [];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }

    public function unarchive()
    {
        $this->update(['archived_at' => null]);
    }

    public function isParticipant(User $user)
    {
        return $this->participants->contains($user);
    }

    public function activeParticipants()
    {
        return $this->participants->where('is_online', true)->get();
    }

    public function isGroup()
    {
        return $this->type === 'group';
    }
    public function isPrivate()
    {
        return $this->type === 'private';
    }
    public function receiver()
    {
        return $this->participants()->where('user_id', '!=', Auth::id())->first();
    }
    public function sender()
    {
        // This returns a query builder that can be used with eager loading
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->where('user_id', Auth::id());
    }
    public function isArchived()
    {
        return $this->archived_at !== null;
    }
}

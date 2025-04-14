<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function isParticipant(User $user){
<<<<<<< HEAD
        return $this->participants->contains($user);
    }

    // public function activeParticipants()
    // {
    //     return $this->participants->where('is_online', true)->get();
    // }
=======
        return $user;
    }

    public function activeParticipants()
    {
        return $this->participants->where('is_online', true)->get();
    }
>>>>>>> origin/main

    public function isGroup()
    {
        return $this->type === 'group';
    }
<<<<<<< HEAD
    public function getOtherParticipant(User $user)
    {
        return $this->participants->where('id', '!=', $user->id)->first();
    }
=======
>>>>>>> origin/main
}

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

    public function ConversationAdmin(){
        return $this->participants()->where("role","admin")->first();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {

        return $this->hasOne(Message::class)->latest();
    }
    public function lastMessageTime()
    {
        return $this->hasOne(Message::class)->latest()->select('created_at');
    }
    public function lastMessageSender()
    {
        return $this->hasOne(Message::class)->latest()->with('sender');
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
        return $this->participants->contains($user);
    }


    public function isGroup()
    {
        return $this->type === 'group';
    }
    public function isPrivate()
    {
        return $this->type === 'private';
    }
    public function receiver(){
        return $this->participants()->where('user_id', '!=', Auth::id())->first();
    }
    public function sender(){
        return $this->participants()->where('user_id', Auth::id())->first();
    }
    public function isArchived()
    {
        return $this->archived_at !== null;
    }

    public function ConversationName(){
        if ($this->isGroup()) {
            return $this->name;
        } else {
            $receiver = $this->receiver();
            return $receiver ? $receiver->name : 'Unknown';
        }
    }
}

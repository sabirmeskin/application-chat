<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{

    use SoftDeletes;

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


    public function ConversationName(){
        if ($this->isGroup()) {
            return $this->name;
        } else {
            $receiver = $this->receiver();
            return $receiver ? $receiver->name : 'Unknown';
        }
    }
    public function archivedBy()
{
    return $this->belongsToMany(User::class, 'archived_conversations');
}
public function archivedConversations()
{
    return $this->belongsToMany(Conversation::class, 'archived_conversations');
}

public function archive($userId = null)
{
    $userId = $userId ?? Auth::id();
    $this->archivedBy()->syncWithoutDetaching([$userId]);
}

public function unarchive($userId = null)
{
    $userId = $userId ?? Auth::id();
    $this->archivedBy()->detach($userId);
}

public function isArchived($userId = null): bool
{
    $userId = $userId ?? Auth::id();
    return $this->archivedBy()->where('user_id', $userId)->exists();
}
public function isDeletedForUser($userId = null)
{
    $userId = $userId ?? auth()->id();

    return $this->participants()
        ->where('user_id', $userId)
        ->whereNotNull('deleted_at')
        ->exists();
}

}

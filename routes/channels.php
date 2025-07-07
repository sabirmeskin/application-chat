<?php

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation' , function(){
    return true;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {

    $conversation = Conversation::find($conversationId);

    return $conversation && $conversation->participants->contains($user->id);
});

Broadcast::channel('chat.{conversationId}',function($user , $conversationId) {
    return $user->conversations()->whereHas('participants', function ($q) use ($conversationId) {
        $q->where('conversation_id', $conversationId);
    })->exists();

});

Broadcast::channel('read.{conversationId}',function($user , $conversationId) {
    return $user->conversations()->whereHas('participants', function ($q) use ($conversationId) {
        $q->where('conversation_id', $conversationId);
    })->exists();
    // return true;
});
Broadcast::channel('typing.{conversationId}', function ($user, $conversationId) {
    return $user->conversations()->whereHas('participants', function ($q) use ($conversationId) {
        $q->where('conversation_id', $conversationId);
    })->exists();
});

Broadcast::channel('user-status.{userId}', function ($user, $userId) {
    if ((int) $user->id !== (int) $userId) {
        return null; // unauthorized
    }

    // this array becomes each entry in your `here()` callback
    return [
        'id'   => $user->id,
        'name' => $user->name,
        'status' => $user->is_online ? 'online' : 'offline',
    ];
});
Broadcast::channel('user-status', function ($user) {


    // this array becomes each entry in your `here()` callback
    return [
        'id'   => $user->id,
        'name' => $user->name,
        'status' => $user->is_online ? 'online' : 'offline',
    ];
});

Broadcast::channel('message', function ($user) {
    return $user->id === Auth::id();
});



Broadcast::channel('EditMessage.{conversationId}',function($user , $conversationId) {
    return $user->conversations()->whereHas('participants', function ($q) use ($conversationId) {
        $q->where('conversation_id', $conversationId);
    })->exists();

});

Broadcast::channel('chat-sidebar.{userId}', function ($user, $userId) {
    // return (int) $user->id === (int) $userId;
   return  true;
});

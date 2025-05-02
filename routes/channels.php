<?php

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
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

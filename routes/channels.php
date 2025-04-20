<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return $user->conversations()->whereHas('participants', function ($q) use ($conversationId) {
        $q->where('conversation_id', $conversationId);
    })->exists();
});

Broadcast::channel('conversation', function () {
    return true;
});
// Broadcast::channel('test', function () {
//     return true;
// });

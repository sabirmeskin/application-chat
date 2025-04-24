<?php

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation', function () {
    return true;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // Check if the user is a participant in the conversation
    return \App\Models\Conversation::where('id', $conversationId)
        ->whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->exists();
});
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }
    $participant = ConversationParticipant::where('user_id', $user->id)
        ->where('conversation_id', $conversationId)
        ->first();
    return $participant !== null;
});

Broadcast::channel('presence.{conversationId}', function ($user, $conversationId) {
    return Conversation::where('id', $conversationId)
        ->whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->exists();
});
Broadcast::channel('chat', function () {
    return true;
});

Broadcast::channel('users.online', function ($user) {
    if ($user) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});
Broadcast::channel('user.active', function ($user) {
    if ($user) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});

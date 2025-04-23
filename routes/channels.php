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

Broadcast::channel('chat.{conversationId}',function($user , $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }
    $participant = ConversationParticipant::where('user_id', $user->id)
        ->where('conversation_id', $conversationId)
        ->first();
    return $participant !== null;
});

// Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
//     $conversation = Conversation::find($conversationId);
//     if (!$conversation) {
//         return false;
//     }
//     $participant = ConversationParticipant::where('user_id', $user->id)
//         ->where('conversation_id', $conversationId)
//         ->first();
//     return $participant !== null;
// });

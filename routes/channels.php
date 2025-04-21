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

// Broadcast::channel('conversation', function () {
//     return true;
// });

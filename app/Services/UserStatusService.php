<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;

class UserStatusService
{
    public function updateUserStatus(User $user, $status)
    {
        $user->is_online = $status;
        $user->save();
    }
    public function getUsersStatus($users)
    {
        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'is_online' => $user->is_online,
                'last_seen_at' => $user->last_seen_at,
            ];
        });
    }
  
    public function updateUserPresence(User $user, Conversation $conversation, $presence)
    {
        $user->conversations()->updateExistingPivot($conversation->id, ['is_active' => $presence]);
    }
 
}

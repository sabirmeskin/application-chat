<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ConversationService
{
    public function createPrivateConversation(User $sender,User $receiver,$encrypted = false):Conversation
    {
        // Business logic for creating a conversation

        $conversation =  Conversation::create([
            'name' => $sender->name . ' & ' . $receiver->name,
            'type' => 'private',
            'encrypted' => $encrypted,
        ]);
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $sender->id,
        ]);
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $receiver->id,
        ]);
        return $conversation;

    }

    public function createGroupConversation($name,User $admin, array $users,$encrypted = false):Conversation
    {
        $conversation =  Conversation::create([
            'name' => $name ?? 'Group Chat',
            'type' => 'group',
            'encrypted' => $encrypted,
        ]);
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        foreach ($users as $user) {
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'role' => 'member',
            ]);
        }
        return $conversation;
    }
    public function getConversationsForUser(User $user,$includeArchived = false): Collection
    {
        $query = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
        if ($includeArchived) {
            $query->orWhereHas('archivedConversations', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }
        $conversations = $query->with(['participants', 'messages'])->get();

        return $conversations;

    }
    public function getConversationWithMessages(Conversation $conversation,$int):Conversation
    {
        $conversation = Conversation::with(['messages' => function ($query) use ($int) {
            $query->orderBy('created_at', 'desc')->take($int);
        }])->find($conversation->id);

        return $conversation;

    }
    public function archiveConversation(User $user,Conversation $conversation):Conversation
    {
        $conversation->archive();
        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['archived_at' => now()]);

        return $conversation;
    }
    public function unarchiveConversation(User $user,Conversation $conversation):Conversation
    {
        $conversation->unarchive();
        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['archived_at' => null]);

        return $conversation;
    }
    public function getPrivateConversationBetween(User $sender,User $receiver):Conversation
    {
        $conversation = Conversation::where('type', 'private')
            ->whereHas('participants', function ($query) use ($sender, $receiver) {
                $query->where('user_id', $sender->id)
                    ->orWhere('user_id', $receiver->id);
            })
            ->first();

        return $conversation;
    }
    // public function isUserInConversation(User $user, Conversation $conversation): bool
    // {
    //     return $conversation->participants->contains($user);
    // }
    // public function getActiveParticipants(Conversation $conversation): array
    // {
    //     return $conversation->activeParticipants()->get();
    // }
    // public function isGroupConversation(Conversation $conversation): bool
    // {
    //     return $conversation->isGroup();
    // }
    // public function getLastMessage(Conversation $conversation)
    // {
    //     return $conversation->lastMessage()->first();
    // }

    // Add other conversation-related methods
}

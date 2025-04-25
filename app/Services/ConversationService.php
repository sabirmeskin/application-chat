<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Events\ConversationCreatedEvent;
use App\Events\ConversationUpdatedEvent;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    /**
     * Get the instance of the ConversationService.
     * hadi singleton
     * @return self
     */
    public static function getInstance(): self
    {
        return App::make(self::class);
    }

    public function __construct()
    {
        // Initialize any dependencies or properties here
    }

    public function createPrivateConversation(User $sender,User $receiver,$encrypted = false):Conversation
    {
        // Check if a private conversation already exists between the sender and receiver
        $existingConversation = Conversation::where('type', 'private')
            ->whereHas('participants', function ($query) use ($sender) {
            $query->where('user_id', $sender->id);
            })
            ->whereHas('participants', function ($query) use ($receiver) {
            $query->where('user_id', $receiver->id);
            })
            ->first();

        if ($existingConversation) {
            broadcast(new ConversationCreatedEvent($existingConversation));

            return $existingConversation;
        }

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

        broadcast(new ConversationCreatedEvent($conversation));

        return $conversation;

    }

    public function createGroupConversation($name,User $admin, array $users,$encrypted = false):Conversation
    {


        // Check if a group conversation already exists with the same name
        $existingConversation = Conversation::where('type', 'group')
            ->where('name', $name)
            ->first();
        if ($existingConversation) {
            broadcast(new ConversationCreatedEvent($existingConversation));
            return $existingConversation;
        }
        // Create a new group conversation
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
            $user = User::find($user);
            if (!$user) {
                continue; // Skip if user not found
            }
            // Check if the user is already a participant
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'role' => 'member',
            ]);
        }

        broadcast(new ConversationCreatedEvent($conversation));

        return $conversation;
    }
    public function getConversationsForUser(User $user, $includeArchived = false): Collection
    {
        $query = Conversation::query()
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        if ($includeArchived) {
            $query->orWhereHas('archivedConversations', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $conversations = $query->with(['participants', 'messages'])
            ->leftJoinSub(
                Message::query()
                    ->select('conversation_id', DB::raw('MAX(created_at) as last_message_at'))
                    ->groupBy('conversation_id'),
                'latest_messages',
                'conversations.id',
                '=',
                'latest_messages.conversation_id'
            )
            ->orderByDesc('last_message_at')
            ->get();

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
    public function getGroupConversationsForUser(User $user): Collection
    {
        return Conversation::where('type', 'group')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['participants', 'messages'])
            ->get();
    }
    public function getPrivateConversationsForUser(User $user): Collection
    {
        return Conversation::where('type', 'private')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['participants', 'messages'])
            ->get();
    }
    public function updateGroupConversation(Conversation $conversation,string $name, array $newParticipants): Conversation
    {
        // Check if the conversation is a group conversation
        if ($conversation->type !== 'group') {
            throw new \Exception('This method is only applicable to group conversations.');
        }
        // Check if the user is an admin of the conversation
        $adminParticipant = $conversation->ConversationAdmin();
        // dd($adminParticipant);
        if (!$adminParticipant || $adminParticipant->id != Auth::id()) {
            throw new \Exception('Only admins can edit group conversation participants.');
        }
        $conversation->update(['name' => $name]);
        $conversation->participants()->syncWithoutDetaching(
            $newParticipants
        );
        broadcast(new ConversationUpdatedEvent($conversation));
        return $conversation;
    }

}

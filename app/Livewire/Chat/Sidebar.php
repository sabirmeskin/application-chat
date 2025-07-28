<?php

namespace App\Livewire\Chat;

use App\Events\MessageSentEvent;
use App\Models\Conversation;
use App\Models\ConversationUserLog;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\ConversationService;
use Livewire\Attributes\On;

class Sidebar extends Component
{

    public $conversations = [];
    public $activeId;
    public $user;


    protected $conversationService;

    public function mount(ConversationService $conversationService)
    {

        $this->conversationService = $conversationService;
        $this->loadConversations();
        $this->user = Auth::user();
    }
    public function loadConversations()
    {
        $this->conversations = ConversationService::getInstance()->getConversationsForUser(Auth::user(), false);
    }

    public function toggleActive($conversationId)
    {
        $this->activeId = $conversationId;
        $this->dispatch('conversationSelected', $conversationId);
    }

    public function removeConversation($conversationId)
    {
        // Remove the conversation from the current list
        $this->conversations = collect($this->conversations)
            ->reject(fn($c) => $c->id == $conversationId)
            ->values();
        // $this->loadConversations();
        $this->dispatch('refresh');
        // ->all();
        // dd($this->conversations);
    }

    public function refreshConversations()
    {
        $this->loadConversations();
    }

    public function getListeners()
    {
        $userId =  Auth::id();
        return [
            'echo:private-conversation,ConversationCreatedEvent' => 'updateConversations',
            'conversationDeleted' => 'removeConversation',
            "echo-private:chat-sidebar.{$userId},UserRejoinedConversationEvent" => 'refreshConversations',

        ];
    }


    // public function userStatusOnLine($user)
    // {
    //     dd($user);
    //     $this->conversation->users()->updateExistingPivot($user['id'], ['is_online' => true]);
    // }
    // public function handleUpdateConversationEvent(){
    //     $this->conversations = ConversationService::getInstance()->getConversationsForUser(Auth::user(), false);
    // }
    public function hydrate() {}

    // public function updateConversations($event)
    // {

    //     $newConversation = Conversation::find($event['conversation']['id']);
    //     dd($newConversation->participants);
    //     if ($newConversation->isParticipant(Auth::user()) &&
    //         !collect($this->conversations)->contains('id', $newConversation->id)) {
    //         $this->conversations[] = $newConversation;

    //     }
    // }
    public function updateConversations($event)
    {
        $conversationId = $event['conversation']['id'] ?? null;

        if (! $conversationId) {
            return;
        }

        $conversation = Conversation::with('participants')->find($conversationId);
        if (! $conversation) {
            return;
        }

        $user = Auth::user();

        // Check if the user is a participant (even if soft-deleted)
        $participant = $conversation->participants()
            ->where('user_id', $user->id)
            ->withPivot('deleted_at')
            ->first();

        if (! $participant) {
            return; // User is not part of the conversation
        }

        // If user was soft-deleted from conversation, restore them
        if ($participant->pivot->deleted_at !== null) {
            $conversation->participants()->updateExistingPivot($user->id, [
                'deleted_at' => null,
            ]);

            ConversationUserLog::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'action' => 'rejoined',
                'action_at' => now(),
                'note' => 'Rejoined via ConversationCreatedEvent',
            ]);
        }

        // Add to local conversation list if it's not already included
        if (! collect($this->conversations)->contains('id', $conversation->id)) {
            $this->conversations[] = $conversation;
        }
    }




    public function render()
    {
        return view('livewire.chat.sidebar');
    }
}

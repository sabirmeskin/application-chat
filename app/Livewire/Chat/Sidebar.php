<?php

namespace App\Livewire\Chat;

use App\Events\UserActiveInConversationEvent;
use App\Models\Conversation;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\ConversationService;

class Sidebar extends Component
{

    public $conversations = [];
    // public $conversationId;
    public $users=[];
    public $activeId;
    protected $conversationService;

    public function mount(ConversationService $conversationService)
    {
        Auth::user()->markAsOnline();
        // Get initial list of online users
        $this->users = User::where('is_online', true)->get();
        $this->conversationService = $conversationService;
        $this->loadConversations();
    }

    public function loadConversations()
    {
        $this->conversations = $this->conversationService->getConversationsForUser(Auth::user(), false);
    }

    public function toggleActive($conversationId)
    {
        $this->activeId = $conversationId;
        $this->dispatch('conversationSelected', $conversationId);
        broadcast(
            new UserActiveInConversationEvent(
                Auth::user(),
                $conversationId
            )
        );
    }

    public function getListeners()
    {
        return [
            'echo:private-conversation,ConversationCreatedEvent' => 'UpdateConversations',
            'echo-presence:users.online,here' => 'handleUsersHere',
            'echo-presence:users.online,joining' => 'handleUserJoining',
            'echo-presence:users.online,leaving' => 'handleUserLeaving',
            'echo:users.online,UserOnlineStatusChanged' => 'handleStatusChange',
        ];
        

        // Add the listeners to the component

    }
 
    public function handleUsersHere($event)
    {
        $userIds = collect($event)->pluck('id');
        $this->users = User::whereIn('id', $userIds)->get();
    }
    public function handleUserJoining($user)
    {
        // When a new user joins the channel
        $newUser = User::find($user['id']);
        if ($newUser) {
            $newUser->markAsOnline();
            $this->users = User::where('is_online', true)->get();
        }
    }
    public function handleUserLeaving($user)
    {
        // When a user leaves the channel
        $leavingUser = User::find($user['id']);
        if ($leavingUser) {
            $leavingUser->markAsOffline();
            $this->users = User::where('is_online', true)->get();
        }
    }
    public function handleStatusChange($event)
    {
        $this->users = User::where('is_online', true)->get();
    }
    public function UpdateConversations($event)
    {
        $newConversation = Conversation::find($event['conversation']['id']);
        if (
            $newConversation->isParticipant(Auth::user()) &&
            !collect($this->conversations)->contains('id', $newConversation->id)
        ) {
            $this->conversations[] = $newConversation;
        }
    }

    public function render()
    {
        return view('livewire.chat.sidebar');
    }
}

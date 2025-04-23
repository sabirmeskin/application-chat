<?php

namespace App\Livewire\Chat;

use App\Events\UserActiveInConversationEvent;
use App\Models\Conversation;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\ConversationService;
use App\Services\UserStatusService;

class Sidebar extends Component
{

    public $conversations = [];
    public $users=[];
    public $activeId;
    protected $conversationService;
    protected $userStatusService;

    public function mount(ConversationService $conversationService, UserStatusService $userStatusService)
    {
        $this->users = User::where('is_online', true)->get();
        $this->conversationService = $conversationService;
        $this->userStatusService = $userStatusService;
        $this->loadConversations();
    }
    
    public function hydrate()
    {
        $this->conversationService = app(ConversationService::class);
        $this->userStatusService = app(UserStatusService::class);
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
    }
 
    public function handleUsersHere($event)
    {
        $userIds = collect($event)->pluck('id');
        $this->userStatusService->getUsersStatus(
            User::whereIn('id', $userIds)->get()
        );
        $this->users = User::where('is_online', true)->get();
    }
    public function handleUserJoining($user)
    {
        $this->userStatusService->updateUserStatus(
            User::find($user['id']),
            true
        );
        $this->users = User::where('is_online', true)->get();
    }
    public function handleUserLeaving($user)
    {
        $this->userStatusService->updateUserStatus(
            User::find($user['id']),
            false
        );
        $this->users = User::where('is_online', true)->get();
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

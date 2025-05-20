<?php

namespace App\Livewire\Chat;

use App\Events\MessageSentEvent;
use App\Models\Conversation;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\ConversationService;
use Livewire\Attributes\On;

class Sidebar extends Component
{

    public $conversations = [];
    public $activeId ;
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

    public function getListeners()
    {
        $userId =  Auth::id();
       return [
        'echo:private-conversation,ConversationCreatedEvent' => 'updateConversations',
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
    public function hydrate(){

    }

    public function updateConversations($event)
    {

        $newConversation = Conversation::find($event['conversation']['id']);
        if ($newConversation->isParticipant(Auth::user()) &&
            !collect($this->conversations)->contains('id', $newConversation->id)) {
            $this->conversations[] = $newConversation;

        }
    }


    public function render()
    {
        return view('livewire.chat.sidebar');
    }
}

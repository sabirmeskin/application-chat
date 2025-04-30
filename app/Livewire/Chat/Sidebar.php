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


    protected $conversationService;

    public function mount(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
        $this->loadConversations();
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
       return [
        'echo:private-conversation,ConversationCreatedEvent' => 'UpdateConversations',
        // 'echo:private-conversation,ConversationUpdatedEvent' => 'handleUpdateConversationEvent',
        "echo:private-chat,MessageSentEvent" => 'handleNewMessage',
       ];
    }
    public function handleNewMessage($event)
    {
        dd('event');
        $conversationId = $event['message']['conversation_id'];

        // Transmet l'événement au composant Convo correspondant
        $this->dispatchTo(
            "chat.components.convo",
            "refreshConvo",
            conversationId: $conversationId
        );
    }
    // public function handleUpdateConversationEvent(){
    //     $this->conversations = ConversationService::getInstance()->getConversationsForUser(Auth::user(), false);
    // }
    public function hydrate(){

    }

    public function UpdateConversations($event)
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

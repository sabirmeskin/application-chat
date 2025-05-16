<?php

namespace App\Livewire\Chat\Modals;

use App\Models\Message;
use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class EditMessage extends Component
{
    public $listeners = ['showModal'];

    public $contacts = [];
    public $search = '';
    public $message;
    public $recipientId;

    public function mount()
    {
        $this->contacts = User::where('id', '!=', Auth::id())->get();
    }

    #[On('showModal')]
    public function showModal($messageId)
    {
        $this->message = Message::findOrFail($messageId);
        $this->dispatch('open-modal', name: 'edit-message');
    }

    

    public function updateUsers()
    {
        $this->contacts = User::where('name', 'like', "%{$this->search}%")
                              ->where('id', '!=', Auth::id())
                              ->get();
    }

    public function selectContact($contactId)
    {
        $conversation = $this->createConversation(User::findOrFail($contactId));

        // après création de la conv, on affiche la modale de transfert (ou on la ferme)
        $this->dispatch('close-modal', ['name' => 'edit-message']);
        $this->dispatch('message-transferred', [
            'conversationId' => $conversation->id,
        ]);
    }

    protected function createConversation(User $user)
    {
        return ConversationService::getInstance()
                ->createPrivateConversation(Auth::user(), $user, false);
    }

    public function render()
    {
        return view('livewire.chat.modals.edit-message');
    }
}

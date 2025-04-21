<?php

namespace App\Livewire\Chat\Modals;

use App\Models\User;
use App\Services\ConversationService;
use Livewire\Component;
use Illuminate\Support\Facades\App;

class ContactsModal extends Component
{

    public $contacts = [] ;
    public $search = '';

    public function mount()
    {
        $this->contacts = User::where('id', '!=', auth()->user()->id)->get();
    }

    public function updateUsers()
    {
        $this->contacts = User::where('name', 'like', '%' . $this->search . '%')
            ->get()
            ->except(auth()->user()->id);
    }
    public function selectContact($contactId)
    {
        $user = User::find($contactId);
        $conversation = $this->createConversation($user);
        if ($conversation) {
            $this->dispatch('openConversation', ['conversation'=>$conversation]);
        }
        $this->dispatch('closeModal');

    }

    public function createConversation(User $user)
    {
        $conversationService = ConversationService::getInstance();
        if ($user) {
           return $conversationService->createPrivateConversation(
                auth()->user(),
                $user,
                false
            );
        }
    }

    public function render()
    {
        return view('livewire.chat.modals.contacts-modal');
    }
}

<?php

namespace App\Livewire\Chat\Modals;

use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ContactsModal extends Component
{

    public $contacts = [] ;
    public $search = '';

    public function mount()
    {
        $this->contacts = User::where('id', '!=', Auth::user()->id)->get();
    }

    public function updateUsers()
    {
        $this->contacts = User::where('name', 'like', '%' . $this->search . '%')
            ->get()
            ->except(Auth::user()->id);
    }
    public function selectContact($contactId)
    {
        $user = User::find($contactId);
        $conversation = $this->createConversation($user);

    }

    public function createConversation(User $user)
    {
        $conversationService = ConversationService::getInstance();
        if ($user) {
           return $conversationService->createPrivateConversation(
                Auth::user(),
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

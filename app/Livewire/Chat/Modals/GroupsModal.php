<?php

namespace App\Livewire\Chat\Modals;

use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupsModal extends Component
{
    public $contacts = [] ;
    public $search = '';
    public $selectedUsers = [];
    public $nom = '';

    
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
    
    public function createConversation()
    {
        $this->validate([
            'nom' => 'required|string|max:255',
            'selectedUsers' => 'required|array|min:2',
        ]);
        // if (empty($this->nom)) {
        //     $this->addError('nom', 'Le nom du groupe est requis.');
        //         return;
        // }

        // if (count($this->selectedUsers) < 2) {
        //     $this->addError('noUsers', 'Veuillez sélectionner au moins deux utilisateurs.');
        //     return;
        // }
        $this->dispatch('closeModal');
        $conversationService = ConversationService::getInstance();
        $conversation =  $conversationService->createGroupConversation(
            $this->nom, Auth::user(), $this->selectedUsers, false
        );
        $this->dispatch('conversationCreated', conversationId: $conversation->id);
        // dd($conversation);
        return $conversation;
    }

    public function render()
    {
        return view('livewire.chat.modals.groups-modal');
    }
}

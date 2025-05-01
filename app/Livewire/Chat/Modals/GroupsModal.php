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
            'nom' => 'required|string|max:255|min:3',
            'selectedUsers' => 'required|array|min:2',
        ], [
            'nom.required' => 'Le nom du groupe est requis.',
            'nom.string' => 'Le nom du groupe doit être une chaîne de caractères.',
            'nom.max' => 'Le nom du groupe ne peut pas dépasser 255 caractères.',
            'selectedUsers.required' => 'Vous devez sélectionner au moins deux utilisateurs.',
            'selectedUsers.array' => 'Les utilisateurs sélectionnés doivent être sous forme de tableau.',
            'selectedUsers.min' => 'Vous devez sélectionner au moins deux utilisateurs.',
        ]);

        $this->dispatch('closeModal');
        $conversationService = ConversationService::getInstance();

        return $conversationService->createGroupConversation(
                $this->nom, Auth::user(), $this->selectedUsers, false
        );

    }

    public function render()
    {
        return view('livewire.chat.modals.groups-modal');
    }
}

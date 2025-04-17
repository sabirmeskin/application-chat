<?php
namespace App\Livewire\Chat\Modals;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditGroupModal extends Component
{
    public Conversation $conversation;
    public $nom = '';
    public $selectedUsers = [];
    public $contacts = [];
    public $search = '';

    public function mount(Conversation $conversation)
    {
        $this->conversation = $conversation;
        $this->nom = $conversation->name;

        $this->selectedUsers = $conversation->participants()
            ->where('user_id', '!=', Auth::id())
            ->pluck('user_id')
            ->toArray();

        $this->contacts = User::where('id', '!=', Auth::id())->get();
    }   

    public function updateUsers()
    {
        $this->contacts = User::where('name', 'like', '%' . $this->search . '%')
            ->where('id', '!=', Auth::id())
            ->get();
    }

    public function save()
    {
        $this->validate([
            'nom' => 'required|string|max:2',
            'selectedUsers' => 'required|array|min:2',
        ]);
        $this->dispatch('closeModal');
        $this->dispatch('groupUpdated');
        $conversationService = ConversationService::getInstance();
        return $conversationService->updateGroupConversation(
            $this->conversation,
            $this->nom,
            $this->selectedUsers
        );
    }

    public function render()
    {
        return view('livewire.chat.modals.edit-group-modal');
    }
}

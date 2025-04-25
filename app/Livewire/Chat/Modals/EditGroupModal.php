<?php
namespace App\Livewire\Chat\Modals;

 use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class EditGroupModal extends Component
{
    public $conversation;
    public $nom = '';
    public $selectedUsers = [];
    public $contacts = [];
    public $search = '';
    public function mount($conversation)
    {
        $this->conversation = $conversation;
        $this->resetFields();

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
            'nom' => 'required|string|min:2',
            'selectedUsers' => 'required|array|min:2',
        ]);
        $this->dispatch('closeModal');


        $conversationService = ConversationService::getInstance();
        return $conversationService->updateGroupConversation(
            $this->conversation,
            $this->nom,
            $this->selectedUsers
        );
    }

    #[On('resetModal')]
    public function resetFields()
    {
    $this->nom = $this->conversation->name;

    $this->selectedUsers = $this->conversation->participants()
        ->where('user_id', '!=', Auth::id())
        ->pluck('user_id')
        ->toArray();

    $this->contacts = \App\Models\User::where('id', '!=', Auth::id())->get();
    $this->search = '';
}

    public function render()
    {
        return view('livewire.chat.modals.edit-group-modal');
    }
}

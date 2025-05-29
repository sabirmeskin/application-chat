<?php

namespace App\Livewire\Chat\Modals;

use App\Models\Message;
use App\Models\User;
use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ForwardMessageModal extends Component
{
    public $contacts    = [];
    public $message;
    public $search      = '';

    public function mount()
    {
        $this->contacts = User::where('id', '!=', Auth::id())->get();
    }

    public function updateUsers()
    {
        $this->contacts = User::where('name', 'like', "%{$this->search}%")
            ->where('id', '!=', Auth::id())
            ->get();
    }

    public function selectContact($contactId)
    {
        $user         = User::findOrFail($contactId);
        $conversation = $this->getOrCreateConversation($user);

        // now actually forward the message into $conversation:
        $this->forwardIntoConversation($conversation);

        // tell the parent or listener which conversation we ended up in
        $this->dispatch('conversationSelected', $conversation->id);
        $this->dispatch('messageForwarded', $conversation->id);
        $this->modal('forward-message-modal')->close();
    }

   protected function getOrCreateConversation(User $user): Conversation
{
    // 1) Try to find an existing 1:1 private conversation
    $existing = Conversation::where('type', 'private')
        ->whereHas('participants', fn($q) => $q->where('user_id', Auth::id()))
        ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
        ->withCount('participants')
        ->having('participants_count', 2) // Ensure exactly two participants
        ->first();

    if ($existing) {
        return $existing;
    }

    // 2) Otherwise create a new private conversation
    return ConversationService::getInstance()
        ->createPrivateConversation(Auth::user(), $user, false);
}

    protected function forwardIntoConversation(Conversation $conversation)
{
    // Find the other participant (receiver) in this conversation
    $receiverId = $conversation->participants()
                    ->where('user_id', '!=', Auth::id())
                    ->pluck('user_id')
                    ->first();

    Message::create([
        'conversation_id' => $conversation->id,
        'sender_id'       => Auth::id(),
        'receiver_id'     => $receiverId,  // Add this here
        'body'            => $this->message->body,
        // …copy any other fields you want…
    ]);
}


    #[On('forwardMessage')]
    public function forwardMessage(int $messageId)
    {
        $this->message = Message::findOrFail($messageId);
        $this->modal('forward-message-modal')->show();
    }

    public function render()
    {
        return view('livewire.chat.modals.forward-message-modal');
    }
}

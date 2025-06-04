<?php

namespace App\Livewire\Chat\Modals;

use App\Events\MessageForwardedEvent;
use App\Models\Message;
use App\Models\User;
use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ForwardMessageModal extends Component
{
    public array $items       = [];     // merged contacts + groups
    public        $message;
    public string $search      = '';

    public function mount()
    {
         $this->updateItems();
    }
public function updatedSearch($value)
{
    $this->updateItems();
}

    public function updateItems()
{
    $search = '%' . $this->search . '%';

    // 1) Users
    $users = User::where('id', '!=', Auth::id())
        ->where('name', 'like', $search)
        ->get()
        ->each(fn($u) => $u->setAttribute('type', 'user'));

    // 2) Groups
    $groups = Conversation::where('type', 'group')
        ->where('name', 'like', $search)
        ->get()
        ->each(fn($g) => $g->setAttribute('type', 'group'));

    // Merge them
    $this->items = $users->concat($groups)->values()->all();
}


    protected function loadItems(): void
    {
        // 1) Fetch users (private contacts)
        $users = User::where('id', '!=', Auth::id())
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->get()
            ->map(fn(User $u) => [
                'type'  => 'user',
                'id'    => $u->id,
                'label' => $u->name,
                'model' => $u,
            ]);

        // 2) Fetch groups
        $groups = Conversation::where('type', 'group')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->get()
            ->map(fn(Conversation $c) => [
                'type'  => 'group',
                'id'    => $c->id,
                'label' => $c->name,
                'model' => $c,
            ]);

        // 3) Merge into one array
        $this->items = $users->concat($groups)->toArray();
    }


    #[On('forwardMessage')]
    public function forwardMessage(int $messageId)
    {
        $this->message = Message::findOrFail($messageId);
        // dd($this->message->type);
        $this->modal('forward-message-modal')->show();
    }

    public function selectItem(int $id, string $type)
    {
        if ($type == null) {
            $user         = User::findOrFail($id);
            $conversation = $this->getOrCreateConversation($user);
        } else {
            $conversation = Conversation::findOrFail($id);
            if (! $conversation->isParticipant(Auth::user())) {
                $this->dispatch('error', 'You are not a participant in this group.');
                return;
            }
        }

        $this->forwardIntoConversation($conversation);

        $this->dispatch('conversationSelected', $conversation->id);
        $this->dispatch('messageForwarded',  $conversation->id);
        $this->modal('forward-message-modal')->close();
    }

    protected function getOrCreateConversation(User $user): Conversation
    {
        $existing = Conversation::where('type', 'private')
            ->whereHas('participants', fn($q) => $q->where('user_id', Auth::id()))
            ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->withCount('participants')
            ->having('participants_count', 2)
            ->first();

        return $existing
            ?? ConversationService::getInstance()
                   ->createPrivateConversation(Auth::user(), $user, false);
    }

    protected function forwardIntoConversation(Conversation $conversation)
    {
        
        $receiverId = $conversation->participants()
            ->where('user_id', '!=', Auth::id())
            ->value('user_id');

        $forwardedMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'receiver_id'     => $receiverId,
            'body'            => $this->message->body,
            'type'            => $this->message->type,
        ]);

        foreach ($this->message->getMedia('attachments') as $media) {

            $media->copy($forwardedMessage, 'attachments');
        }

        broadcast(new MessageForwardedEvent($forwardedMessage))->toOthers();
        $this->dispatch('scrollToBottom');

    }

    public function render()
    {
        return view('livewire.chat.modals.forward-message-modal');
    }
}

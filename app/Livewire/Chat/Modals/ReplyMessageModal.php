<?php

namespace App\Livewire\Chat\Modals;

use App\Events\MessageReplyEvent;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ReplyMessageModal extends Component
{
    /** @var Message|null */
    public $message = null;   // message we’re replying to

    public string $content = ''; // bound to the textarea in your modal

    /** Save a reply to the selected message */
    public function reply(): void
    {
        if (! $this->message) {
            return;
        }

        // 1️⃣ basic validation
        $this->validate([
            'content' => 'required|string|max:1000',
        ]);

        // 2️⃣ create the reply
        $reply = $this->message                     // uses Message::replies() relationship
            ->replies()
            ->create([
                'sender_id'   => Auth::id(),
                'receiver_id' => $this->message->sender_id === Auth::id()
                    ? $this->message->receiver_id   // if I wrote the parent, receiver is the other person
                    : $this->message->sender_id,    // otherwise receiver is the original sender
                'body'     => $this->content,
                'parent_id'   => $this->message->id,
            ]);

        // 3️⃣ notify Livewire listeners (front-end)
        $this->dispatch('messageReplied', $reply->id);

        // 4️⃣ broadcast to Echo listeners (other tabs/devices)
        broadcast(new MessageReplyEvent($reply))->toOthers();

        // 5️⃣ clean up UI
        $this->reset(['content', 'message']);
        $this->modal('reply-message-modal')->close();
    }

    /** Called when user clicks “Reply” on an existing message */
    #[On('replyMessage')]
    public function replyMessage(int $messageId): void
    {
        $this->message = Message::find($messageId);

        // Optional: guard against missing or soft-deleted messages
        if (! $this->message) {
            $this->addError('message', 'Original message not found.');
            return;
        }

        $this->modal('reply-message-modal')->show();
    }

    public function render()
    {
        return view('livewire.chat.modals.reply-message-modal');
    }
}

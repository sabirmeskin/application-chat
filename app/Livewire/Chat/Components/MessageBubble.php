<?php

namespace App\Livewire\Chat\Components;

use App\Events\MessageDeletedEvent;
use App\Events\MessageReadEvent;
use App\Models\Message;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageBubble extends Component
{
    public $message;
    public $userId;
    public $avatarOn;
    protected $listeners = ['notify'];

    public function notify($message)
    {
        session()->flash('message', $message);
        // Or use your preferred notification system
        // $this->dispatchBrowserEvent('show-toast', ['message' => $message]);
    }

    public function mount($message,$avatarOn)
    {
        $this->avatarOn = $avatarOn;
        $this->message = $message;
        $this->userId = Auth::id();
    }
    public function getMessageText($messageId)
    {
        try {
            $message = Message::find($messageId);
            return $message ? $message->content : '';
        } catch (Exception $e) {
            return '';
        }
    }

    public function deleteMessage()
    {
        if (!$this->message) {
            return;
        }
        $this->message->delete();
        $this->dispatch('messageDeleted', $this->message);
        broadcast(new MessageDeletedEvent($this->message))->toOthers();
    }
    public function render()
    {
        return view('livewire.chat.components.message-bubble');
    }
    #[On('messageReadRefresh')]
    public function handleMessageReadRefresh()
    {
       $this->message -> refresh();
    }

    #[On('messageEdited')]
    public function handleMessageEdited()
    {
        // This method is triggered when a message is edited
        $this->message->refresh();
    }

}

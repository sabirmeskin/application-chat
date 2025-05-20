<?php

namespace App\Livewire\Chat\Modals;

use App\Events\MessageDeletedEvent;
use Livewire\Component;

class ConfirmDelete extends Component
{
    public $message;
    public $conversation;

        public function delete(){
        $this->dispatch('messageDeleted', $this->message);
        broadcast(new MessageDeletedEvent($this->message))->toOthers();
    }

    public function mount($conversation)
    {
        $this->conversation = $conversation;
    }

    public function render()
    {
        return view('livewire.chat.modals.confirm-delete');
    }
}

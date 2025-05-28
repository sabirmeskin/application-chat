<?php

namespace App\Livewire\Chat\Modals;

use App\Events\MessageDeletedEvent;
use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Component;

class ConfirmDelete extends Component
{
    public $message;


    public function delete()
    {
        if (!$this->message) {
            return;
        }
        $this->message->delete();
        $this->dispatch('messageDeleted', $this->message);
        broadcast(new MessageDeletedEvent($this->message))->toOthers();
        $this->modal('delete-message')->close();

    }

    #[On('confirmDelete')]
    public function confirmDelete($messageid)
    {
        $this->message = Message::find($messageid);
        $this->modal('delete-message')->show();
    }





    public function render()
    {
        return view('livewire.chat.modals.confirm-delete');
    }
}

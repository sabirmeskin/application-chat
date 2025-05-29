<?php

namespace App\Livewire\Chat\Modals;

use App\Events\MessageEditedEvent;
use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Component;

class EditMessage extends Component
{
    public $message;
    public $editedMessage = '';

    public function edit()
    {
        if (!$this->message) {
            return;
        }
        $this->message->update(['body' => $this->editedMessage]);
        $this->modal('edit-message')->close();
        $this->dispatch('messageEdited', $this->message);
        broadcast(new MessageEditedEvent($this->message))->toOthers();
        $this->modal('edit-message')->close();
    }
        #[On('editMessage')]
    public function confirmDelete($messageid)
    {
        $this->message = Message::find($messageid);
        $this->modal('edit-message')->show();
    }


    public function render()
    {
        return view('livewire.chat.modals.edit-message');
    }
}

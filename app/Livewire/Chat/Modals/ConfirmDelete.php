<?php

namespace App\Livewire\Chat\Modals;

use Livewire\Component;

class ConfirmDelete extends Component
{
    public $message;
    public $conversation;

    public function mount($conversation)
    {
        $this->conversation = $conversation;
    }
    public function delete(){

    }

    public function render()
    {
        return view('livewire.chat.modals.confirm-delete');
    }
}

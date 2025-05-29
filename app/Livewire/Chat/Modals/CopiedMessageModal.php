<?php

namespace App\Livewire\Chat\Modals;

use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Component;

class CopiedMessageModal extends Component
{
    public $message;

   public function hide()
    {
        if (!$this->message) {
            return;
        }
        $this->modal('copied-message-modal')->close();

    }

    #[On('copiedMessage')]
    public function copiedMessadeModal($messageId)
    {
        $this->message = Message::find($messageId);
        $this->modal('copied-message-modal')->show();
    }

    public function render()
    {
        return view('livewire.chat.modals.copied-message-modal');
    }
}

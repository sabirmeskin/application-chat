<?php

namespace App\Livewire\Chat\Components\Mimes;

use Livewire\Component;

class Document extends Component
{
    public $message;

    public function mount($message){
        $this->message = $message;
    }
    public function render()
    {
        return view('livewire.chat.components.mimes.document');
    }
}

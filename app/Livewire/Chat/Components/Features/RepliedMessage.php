<?php

namespace App\Livewire\Chat\Components\Features;

use Livewire\Component;

class RepliedMessage extends Component
{
    public $message = null;  

    public function render()
    {
        return view('livewire.chat.components.features.replied-message');
    }
}

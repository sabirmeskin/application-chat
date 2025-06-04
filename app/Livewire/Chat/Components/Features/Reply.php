<?php

namespace App\Livewire\Chat\Components\Features;

use Livewire\Component;

class Reply extends Component
{
    public $message;

    public function render()
    {
        return view('livewire.chat.components.features.reply');
    }
}

<?php

namespace App\Livewire\Chat\Components\Features;

use App\Events\MessageReplyEvent;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Reply extends Component
{

 public $message = null;  
 public $reply= false; 

    public string $content = ''; 

    #[On('replyMessage')]
    public function replyMessage(int $messageId): void
    {
        $this->reply = true; 
        $this->message = Message::find($messageId);
        $this->dispatch('replyToMessage', $messageId);
        $this->dispatch('scrollToBottom');
    }
    #[On('sendReply')]
    public function sendReply(): void
    {
        $this->reply =! $this->reply;
    }
    
    

    public function render()
    {
        return view('livewire.chat.components.features.reply');
    }
}

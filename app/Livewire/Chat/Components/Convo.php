<?php

namespace App\Livewire\Chat\Components;

use Livewire\Attributes\On;
use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Convo extends Component
{
    public $conversation;
    public $onlineUsers = [];


    public function mount($conversation)
    {
        $this->conversation = $conversation;
    }

    public function getListeners()
    {
        return [
            'echo-presence:user-status,here' => 'userListUpdated',
            'echo-presence:user-status,joining' => 'userJoined',
            'echo-presence:user-status,leaving' => 'userLeft',
        ];
    }
    // public function userJoined(array $user)
    // {
    //     // avoid duplicates
    //     if (! collect($this->onlineUsers)->pluck('id')->contains($user['id'])) {
    //         $this->onlineUsers[] = $user;
    //     }
    //     // $this->dispatch('userStatusOnLine', $user);
    // }
    // public function userLeft(array $user)
    // {
    //     $this->onlineUsers = collect($this->onlineUsers)
    //         ->reject(fn($u) => $u['id'] === $user['id'])
    //         ->values()
    //         ->toArray();
    // }
    public function userListUpdated(array $users)
{
    $this->onlineUsers = $users;
}

public function userJoined(array $user)
{
    if (!collect($this->onlineUsers)->pluck('id')->contains($user['id'])) {
        $this->onlineUsers[] = $user;
    }
}

public function userLeft(array $user)
{
    $this->onlineUsers = collect($this->onlineUsers)
        ->reject(fn($u) => $u['id'] === $user['id'])
        ->values()
        ->toArray();
}
    #[On('userStatusOnLine')]
    public function userStatusOnLine($user)
    {
      dd($user);
        $this->conversation->users()->updateExistingPivot($user['id'], ['is_online' => true]);
    }
 
    #[On('refreshConvo')]
    public function refreshConvo($conversationId, $message = null)
    {
        if ($conversationId == $this->conversation->id) {
            $this->conversation->refresh();
            // Optionally use $message for preview updates
        }
    }

    #[On('UpdateConvo')]
    public function updateConvo($conversationId){
        if ($conversationId == $this->conversation->id) {
            $this->conversation->refresh();
        }
    }

    public function render()
    {
        return view('livewire.chat.components.convo');
    }
}

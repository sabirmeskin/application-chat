<?php

namespace App\Livewire\Chat\Components;

use Livewire\Component;

class Status extends Component
{
    public $onlineUsers = [];
    public $conversation;


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

    public function userListUpdated(array $users)
    {
        $this->onlineUsers = $users;
    }
    
    public function userJoined(array $user)
    {
        if (!collect($this->onlineUsers)->pluck('id')->contains($user['id'])) {
            $this->onlineUsers[] = $user;
        }
        $this->dispatch('userStatusOnLine', $user);
    }
    
    public function userLeft(array $user)
    {
        $this->onlineUsers = collect($this->onlineUsers)
            ->reject(fn($u) => $u['id'] === $user['id'])
            ->values()
            ->toArray();
    }
    
    public function render()
    {
        return view('livewire.chat.components.status');
    }
}

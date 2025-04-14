<?php

namespace App\Livewire\Chat;

use App\Events\userLoggedOut;
use Livewire\Attributes\On;
use Livewire\Component;

class Sidebar extends Component
{

    public $users = [];
    public $conversations = [];
    public $conversation ;
    public $onlineUsers = [];
    public $presence = 'neutral';



    public function loadConversations()
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            return;
        }

        // Load conversations for the authenticated user
    $this->conversations = auth()->user()->conversations()
        ->with(['participants', 'lastMessage'])
        ->orderByDesc(function ($query) {
            $query->select('created_at')
                  ->from('messages')
                  ->whereColumn('conversation_id', 'conversations.id')
                  ->latest()
                  ->limit(1);
        })
        ->get();
}



    // public function getListeners()
    // {
    //     $listeners = [];
    //     foreach ($this->conversations as $conversation) {
    //         $listeners["echo-private:conversation.{$conversation->id},MessageSendEvent"] = 'refreshList';
    //     }
    //     return $listeners;
    // }

    public function mount()
    {
        $this->loadConversations();
        $this->dispatch('conversationUpdated');
    }


    public function setConversation($conversationId)
    {
        $this->conversation = auth()->user()->conversations()->find($conversationId);
        // dd($this->conversation->participants);

        $this->dispatch('conversationSelected',$conversationId);
    }

    #[On('conversationStarted')]
    public function handleConversationStarted($conversationId)
        {
            $this->setConversation($conversationId);
            $this->loadConversations();
        }


      #[On('conversationUpdated')]
      public function refreshList()
    {
        // dd('refresh');
        $this->loadConversations();

    }

        public function presenceHere($users)
    {
        $this->onlineUsers = $users;
    }

    public function userJoining($user)
    {
        $this->onlineUsers[] = $user;
        // User::where('id', $user['id'])->update([
        //     'is_online' => true,
        //     'last_seen_at' => now(),
        // ]);
        $this->loadConversations();

        // dd($user['name'] . ' joined');
    }

    public function userLeaving($user)
    {
        $this->onlineUsers = collect($this->onlineUsers)
            ->reject(fn ($u) => $u['id'] === $user['id'])
            ->values()
            ->toArray();
        //  dd($this->onlineUsers);
        // User::where('id', $user['id'])->update([
        //     'is_online' => false,
        //     'last_seen_at' => now(),
        // ]);
        $this->loadConversations();

        // logger($user['name'] . ' left');
    }

    public function userLoggedIn($event)
    {

        $this->presence = 'success';

         dd($event['user']);
        // logger('User logged in event:', $event);
    }
    public function logout()
{
    // auth()->user()->update([
    //     'is_online' => false,
    //     'last_seen_at' => now()
    // ]);

    broadcast(new userLoggedOut(auth()->user()))->toOthers();

}
    public function userLoggedOut($event)
    {
        $this->presence = 'danger';
         dd($event);
    }

    public function getListeners()
    {
        return [
            "echo-presence:chat:here" => 'presenceHere',
            "echo-presence:chat:joining" => 'userJoining',
            "echo-presence:chat:leaving" => 'userLeaving',
            "echo-presence:chat,UserLoggedIn" => 'userLoggedIn',
            "echo-presence:chat,UserLoggedOut" => 'userLoggedOut',
        ];
    }

    public function render()
    {
        return view('livewire.chat.sidebar');
    }
}

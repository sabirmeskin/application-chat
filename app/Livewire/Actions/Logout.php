<?php

namespace App\Livewire\Actions;

use App\Events\UserStatusEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        $user = Auth::guard('web')->user();

        // Broadcast the "offline" status to others
        if ($user) {
            User::Find($user->id)->update([
                'is_online' => false,
                'last_seen_at' => now(),
            ]);
        }
        Auth::guard('web')->logout();
        broadcast(new UserStatusEvent($user, 'offline'))->toOthers();

        Session::invalidate();

        Session::regenerateToken();

        return redirect('/');
    }
}

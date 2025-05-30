<?php

use App\Events\UserStatusEvent;
use App\Http\Controllers\HeartbeatController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/',function (){
    return view('components.layouts.app.chatLayout');
})->middleware('auth')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

Route::post('/broadcast-offline', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $user->update(['is_online' => false]);
        broadcast(new UserStatusEvent($user, 'offline'))->toOthers();
        Auth::logout();
        Session::invalidate();
    }
    return response()->noContent();
})->middleware('auth');


require __DIR__.'/auth.php';

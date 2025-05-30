<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class HeartbeatController extends Controller
{
     public function __invoke(): Response
    {
        Auth::user()->update(['last_seen_at' => now()]);
        return response()->noContent();
    }
}

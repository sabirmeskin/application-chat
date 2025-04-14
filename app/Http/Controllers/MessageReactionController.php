<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageReactionController extends Controller
{
    public function store(Message $message,Request $request)
    {
        // Validate the request
        $request->validate([
            'reaction' => 'required|string',
        ]);

        // Add a reaction to the message
        $message->reactions()->create([
            'user_id' => auth()->id(),
            'reaction' => $request->input('reaction'),
        ]);

        return response()->json($message, 201);
    }
    public function destroy(Message $message, Request $request)
    {
        // Validate the request
        $request->validate([
            'reaction' => 'required|string',
        ]);

        // Remove a reaction from the message
        $message->reactions()->where('user_id', auth()->id())->where('reaction', $request->input('reaction'))->delete();

        return response()->json(null, 204);
    }
}

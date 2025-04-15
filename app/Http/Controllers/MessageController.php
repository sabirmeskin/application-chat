<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Conversation $conversation,Request $request)
    {
        // Fetch all messages for the conversation
        $messages = $conversation->messages()->with('sender')->get();

        return $messages;
    }

    public function store(Conversation $conversation, Request $request)
    {
        // Validate the request
        $request->validate([
            'body' => 'required|string',
        ]);

        // Create a new message
        $message = $conversation->messages()->create([
            'body' => $request->input('body'),
            'sender_id' => auth()->id(),
        ]);

        return response()->json($message, 201);
    }

    public function markAsRead(Message $message)
    {
        // Mark the message as read
        $message->update(['read_at' => now()]);

        return response()->json($message);
    }
    public function destroy(Message $message)
    {
        // Delete the message
        $message->softDelete();

        return response()->json(null, 204);
    }
    public function update(Message $message, Request $request)
    {
        // Validate the request
        $request->validate([
            'body' => 'required|string',
        ]);

        // Update the message
        $message->update([
            'body' => $request->input('body'),
        ]);

        return response()->json($message);
    }
    public function replies(Message $message)
    {
        // Fetch all replies for the message
        $replies = $message->replies()->with('sender')->get();

        return response()->json($replies);
    }
    public function reply(Message $message, Request $request)
    {
        // Validate the request
        $request->validate([
            'body' => 'required|string',
        ]);

        // Create a new reply
        $reply = $message->replies()->create([
            'body' => $request->input('body'),
            'sender_id' => auth()->id(),
        ]);

        return response()->json($reply, 201);
    }
    public function deleteReply(Message $message, Message $reply)
    {
        // Delete the reply
        $reply->softDelete();

        return response()->json(null, 204);
    }
    public function updateReply(Message $message, Message $reply, Request $request)
    {
        // Validate the request
        $request->validate([
            'body' => 'required|string',
        ]);

        // Update the reply
        $reply->update([
            'body' => $request->input('body'),
        ]);

        return response()->json($reply);
    }
}

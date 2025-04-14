<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index()
    {
        // Fetch all conversations for the authenticated user
        $conversations = auth()->user()->conversations()->with('lastMessage')->get();

        return response()->json($conversations);
    }

    public function archived(Request $request)
    {
        // Fetch archived conversations for the authenticated user
        $conversations = auth()->user()->conversations()->onlyTrashed()->get();

        return response()->json($conversations);

    }
    public function show($id)
    {
        // Fetch a specific conversation by ID
        $conversation = auth()->user()->conversations()->with('messages')->findOrFail($id);

        return response()->json($conversation);
    }

    public function store(Request $request)
    {
        // Create a new conversation
        $conversation = auth()->user()->conversations()->create($request->all());

        return response()->json($conversation, 201);
    }

    public function update(Request $request, $id)
    {
        // Update an existing conversation
        $conversation = auth()->user()->conversations()->findOrFail($id);
        $conversation->update($request->all());

        return response()->json($conversation);
    }

    public function destroy($id)
    {
        // Delete a conversation
        $conversation = auth()->user()->conversations()->findOrFail($id);
        $conversation->softDelete();
        return response()->json(null, 204);
    }
    public function archive($id)
    {
        // Archive a conversation
        $conversation = auth()->user()->conversations()->findOrFail($id);
        $conversation->archive();

        return response()->json($conversation);
    }
    public function addParticipants(Conversation $conversation, Request $request)
    {
        // Add participants to a conversation
        $conversation = auth()->user()->conversations()->findOrFail($conversation);
        $participants = $request->input('participants');

        foreach ($participants as $participant) {
            $conversation->participants()->attach($participant);
        }

        return response()->json($conversation);
      
    }
    public function removeParticipant(Conversation $conversation,User $participantId)
    {
        // Remove a participant from a conversation
        $conversation = auth()->user()->conversations()->findOrFail($conversation);
        $conversation->participants()->detach($participantId);
        return response()->json($conversation);
    }
    public function changeRole(Conversation $conversation,User $participantId, Request $request)
    {
        // Change the role of a participant in a conversation
        $conversation = auth()->user()->conversations()->findOrFail($conversation);
        $role = $request->input('role');
        $conversation->participants()->updateExistingPivot($participantId, ['role' => $role]);

        return response()->json($conversation);
    }
    public function leaveConversation(Conversation $conversation)
    {
        // Leave a conversation
        $conversation = auth()->user()->conversations()->findOrFail($conversation);
        $conversation->participants()->detach(auth()->user()->id);

        return response()->json(null, 204);
    }

}

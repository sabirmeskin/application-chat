<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\TypingIndicator;
use Illuminate\Http\Request;

class TypingController extends Controller
{
    public function update(TypingIndicator $typing,Request $request)
  {
     
    $typing->update([
        'user_id' => auth()->id(),
        'conversation_id' => $request->input('conversation_id'),
        'typing_at' => now(),
    ]);

  }
       
}

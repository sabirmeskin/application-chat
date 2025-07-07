<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationUserLog extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'action',
        'action_at',
        'note',
    ];
     public $timestamps = false;
      public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

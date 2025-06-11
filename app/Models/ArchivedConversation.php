<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedConversation extends Model
{
    protected $fillable = [
        'user_id',
        'conversation_id',
        'archived_at',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

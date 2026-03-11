<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    protected $fillable = [
        'room_id',
        'sender_id',
        'original_text',
        'translated_text',
        'target_language',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'sender_id' => 'integer',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Rooms::class, 'room_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(UserSessions::class, 'sender_id');
    }
}

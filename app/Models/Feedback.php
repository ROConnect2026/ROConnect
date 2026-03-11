<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'room_id',
        'session_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'session_id' => 'integer',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Rooms::class, 'room_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSessions::class, 'session_id');
    }
}

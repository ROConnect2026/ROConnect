<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RoomsParticipants extends Model
{
    protected $table = 'rooms_participants';

    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'session_id',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'session_id' => 'integer',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
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

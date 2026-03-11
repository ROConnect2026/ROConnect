<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    protected $fillable = [
        'room_id',
        'reporter_id',
        'reason',
        'details',
        'resolved',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'reporter_id' => 'integer',
        'resolved' => 'boolean',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Rooms::class, 'room_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(UserSessions::class, 'reporter_id');
    }
}

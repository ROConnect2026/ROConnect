<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    protected $fillable = [
        'status',
        'ended_at',
        'room_duration_seconds',
        'is_anonymized',
    ];

    protected $casts = [
        'ended_at' => 'datetime',
        'room_duration_seconds' => 'integer',
        'is_anonymized' => 'boolean',
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(RoomsParticipants::class, 'room_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Messages::class, 'room_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'room_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Reports::class, 'room_id');
    }
}

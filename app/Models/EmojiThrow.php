<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmojiThrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'from_participant_id',
        'to_participant_id',
        'emoji',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function fromParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'from_participant_id');
    }

    public function toParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'to_participant_id');
    }
}


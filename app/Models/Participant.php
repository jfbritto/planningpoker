<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'session_id',
        'is_observer',
        'last_activity',
    ];
    
    protected $casts = [
        'is_observer' => 'boolean',
        'last_activity' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function hasVotedForStory(int $storyId): bool
    {
        return $this->votes()->where('story_id', $storyId)->exists();
    }
}


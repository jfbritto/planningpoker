<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Story extends Model
{
    use HasFactory;

    protected $appends = ['average_vote'];

    protected $fillable = [
        'room_id',
        'title',
        'description',
        'is_revealed',
    ];

    protected $casts = [
        'is_revealed' => 'boolean',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getAverageVoteAttribute(): ?float
    {
        $votes = $this->votes()
            ->whereNotIn('value', ['?', 'coffee'])
            ->get()
            ->map(function ($vote) {
                return is_numeric($vote->value) ? (float) $vote->value : null;
            })
            ->filter()
            ->values();

        if ($votes->isEmpty()) {
            return null;
        }

        return round($votes->sum() / $votes->count(), 2);
    }
}


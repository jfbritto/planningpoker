<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_active',
        'creator_session_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function activeStory()
    {
        // Primeiro tenta pegar uma história não revelada
        $unrevealed = $this->stories()->where('is_revealed', false)->latest()->first();
        
        // Se não houver, pega a última história (mesmo que revelada)
        if (!$unrevealed) {
            return $this->stories()->latest()->first();
        }
        
        return $unrevealed;
    }
    
    public function hasActiveUnrevealedStory()
    {
        return $this->stories()->where('is_revealed', false)->exists();
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}


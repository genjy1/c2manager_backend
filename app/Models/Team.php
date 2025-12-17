<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'country',
        'rating',
        'logo_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'decimal:2',
    ];

    /**
     * Get the players that belong to the team.
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_team')
            ->withPivot(['position', 'joined_at', 'left_at', 'is_captain'])
            ->withTimestamps()
            ->orderByPivot('is_captain', 'desc');
    }

    /**
     * Get the active players (not left the team).
     */
    public function activePlayers(): BelongsToMany
    {
        return $this->players()->whereNull('player_team.left_at');
    }

    /**
     * Get the team captain.
     */
    public function captain(): BelongsToMany
    {
        return $this->players()->wherePivot('is_captain', true);
    }

    /**
     * Get the average rating of team players.
     */
    public function getAveragePlayerRatingAttribute(): float
    {
        return $this->activePlayers()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of active players.
     */
    public function getActivePlayersCountAttribute(): int
    {
        return $this->activePlayers()->count();
    }
}

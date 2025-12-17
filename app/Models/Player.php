<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{

    public const STATUS_FREE_AGENT = 'free_agent';
    public const STATUS_IN_TEAM    = 'in_team';
    public const STATUS_INJURED    = 'injured';

    protected $casts = [
        'player_status' => 'string',
    ];
    protected $fillable = [
        'name',
        'surname',
        'nickname',
        'rating',
        'country',
        'player_status',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PlayerImage::class);
    }

    public function mainImage(): ?PlayerImage
    {
        return $this->images()->latest()->first();
    }

    /**
     * Get the teams that the player belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'player_team')
            ->withPivot(['position', 'joined_at', 'left_at', 'is_captain'])
            ->withTimestamps()
            ->orderByPivot('joined_at', 'desc');
    }

    /**
     * Get the active teams (player hasn't left).
     */
    public function activeTeams(): BelongsToMany
    {
        return $this->teams()->whereNull('player_team.left_at');
    }

    // Accessor для быстрого использования в <img>
    public function getAvatarAttribute(): ?string
    {
        return $this->mainImage()?->image_data_uri;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        ''
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PlayerImage::class);
    }

    public function mainImage(): ?PlayerImage
    {
        return $this->images()->latest()->first();
    }

    // Accessor для быстрого использования в <img>
    public function getAvatarAttribute(): ?string
    {
        return $this->mainImage()?->image_data_uri;
    }
}

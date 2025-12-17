<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    protected $fillable = [
        'name',
        'surname',
        'nickname',
        'rating',
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

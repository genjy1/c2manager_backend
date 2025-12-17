<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerImage extends Model
{
    protected $fillable = [
        'player_id',
        'base64',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    // Accessor для вставки в <img>
    public function getImageDataUriAttribute(): ?string
    {
        return $this->image && $this->mime
            ? "data:{$this->mime};base64,{$this->image}"
            : null;
    }

    // Mutator для автоматической обработки загруженного файла
    public function setImageFileAttribute($file)
    {
        if (!$file) return;

        $this->mime = $file->getMimeType();
        $this->image = base64_encode(file_get_contents($file->getRealPath()));
    }
}

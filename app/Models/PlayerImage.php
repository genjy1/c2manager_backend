<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerImage extends Model
{
    protected $fillable = [
        'player_id',
        'base64',
        'mime_type',
        'filename',
        'size',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    // Accessor для вставки в <img>
    public function getImageDataUriAttribute(): ?string
    {
        return $this->base64 && $this->mime_type
            ? "data:{$this->mime_type};base64,{$this->base64}"
            : null;
    }

    // Mutator для автоматической обработки загруженного файла
    public function setImageFileAttribute($file)
    {
        if (!$file) return;

        $this->mime_type = $file->getMimeType();
        $this->base64 = base64_encode(file_get_contents($file->getRealPath()));
    }
}

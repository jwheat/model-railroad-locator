<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'mediable_type',
    'mediable_id',
    'type',
    'disk',
    'path',
    'url',
    'alt',
    'caption',
    'width',
    'height',
    'sort_order',
])]
class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isEmbed(): bool
    {
        return $this->type === 'video_embed';
    }

    public function publicUrl(): ?string
    {
        if ($this->isEmbed()) {
            return $this->url;
        }

        return $this->path
            ? Storage::disk($this->disk ?: 'public')->url($this->path)
            : null;
    }
}

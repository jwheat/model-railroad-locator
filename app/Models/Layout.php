<?php

namespace App\Models;

use App\LayoutScale;
use App\LayoutVisibility;
use App\Models\Concerns\HasUniqueSlug;
use Database\Factories\LayoutFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'club_id',
    'name',
    'slug',
    'description',
    'scale',
    'era',
    'city',
    'region',
    'country',
    'latitude',
    'longitude',
    'visibility',
    'is_featured',
    'cover_image_path',
])]
class Layout extends Model
{
    /** @use HasFactory<LayoutFactory> */
    use HasFactory, HasUniqueSlug;

    protected function casts(): array
    {
        return [
            'scale' => LayoutScale::class,
            'visibility' => LayoutVisibility::class,
            'latitude' => 'float',
            'longitude' => 'float',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visibility', LayoutVisibility::Public);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        return $query->where(function (Builder $query) use ($like) {
            $query->where('name', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('city', 'like', $like)
                ->orWhere('region', 'like', $like)
                ->orWhere('era', 'like', $like);
        });
    }

    /**
     * Order layouts by distance from a point and optionally restrict the radius.
     * Works on SQLite, MySQL, and Postgres; swap for PostGIS ST_DWithin at scale.
     */
    public function scopeNear(Builder $query, float $latitude, float $longitude, ?float $withinKm = null): Builder
    {
        $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';

        return $query
            ->select('layouts.*')
            ->selectRaw("{$haversine} as distance_km", [$latitude, $longitude, $latitude])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->when($withinKm !== null, fn (Builder $query) => $query->whereRaw(
                "{$haversine} <= ?",
                [$latitude, $longitude, $latitude, $withinKm],
            ))
            ->orderBy('distance_km');
    }

    public function isPublic(): bool
    {
        return $this->visibility === LayoutVisibility::Public;
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image_path
            ? Storage::disk('public')->url($this->cover_image_path)
            : null;
    }
}

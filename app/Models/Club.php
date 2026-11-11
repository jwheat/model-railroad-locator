<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Database\Factories\ClubFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'slug',
    'description',
    'city',
    'region',
    'country',
    'website',
    'logo_path',
    'is_verified',
])]
class Club extends Model
{
    /** @use HasFactory<ClubFactory> */
    use HasFactory, HasUniqueSlug;

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(ClubMembership::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_memberships')
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    public function layouts(): HasMany
    {
        return $this->hasMany(Layout::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path
            ? Storage::disk('public')->url($this->logo_path)
            : null;
    }
}

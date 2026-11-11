<?php

namespace App\Models;

use App\MembershipRole;
use Database\Factories\ClubMembershipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['club_id', 'user_id', 'role', 'joined_at'])]
class ClubMembership extends Model
{
    /** @use HasFactory<ClubMembershipFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'role' => MembershipRole::class,
            'joined_at' => 'datetime',
        ];
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

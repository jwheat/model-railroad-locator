<?php

namespace App;

enum MembershipRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function canManageClub(): bool
    {
        return $this !== self::Member;
    }
}

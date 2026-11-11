<?php

namespace App;

enum ReactionType: string
{
    case Like = 'like';
    case Love = 'love';
    case Haha = 'haha';
    case Wow = 'wow';
    case Sad = 'sad';
    case Angry = 'angry';

    public function emoji(): string
    {
        return match ($this) {
            self::Like => '👍',
            self::Love => '❤️',
            self::Haha => '😄',
            self::Wow => '😮',
            self::Sad => '😢',
            self::Angry => '😠',
        };
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public static function fromEmoji(string $emoji): ?self
    {
        foreach (self::cases() as $reaction) {
            if ($reaction->emoji() === $emoji) {
                return $reaction;
            }
        }

        return null;
    }
}

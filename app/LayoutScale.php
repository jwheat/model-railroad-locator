<?php

namespace App;

enum LayoutScale: string
{
    case Z = 'Z';
    case N = 'N';
    case TT = 'TT';
    case HO = 'HO';
    case OO = 'OO';
    case S = 'S';
    case O = 'O';
    case G = 'G';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Z => 'Z (1:220)',
            self::N => 'N (1:160)',
            self::TT => 'TT (1:120)',
            self::HO => 'HO (1:87)',
            self::OO => 'OO (1:76)',
            self::S => 'S (1:64)',
            self::O => 'O (1:48)',
            self::G => 'G (1:22.5)',
            self::Other => 'Other / mixed',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $scale) => [$scale->value => $scale->label()])
            ->all();
    }
}

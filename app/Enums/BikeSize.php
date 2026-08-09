<?php

namespace App\Enums;

enum BikeSize: string
{
    case XS = 'XS';
    case S = 'S';
    case M = 'M';
    case ML = 'M/L';
    case L = 'L';
    case XL = 'XL';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_map(fn (self $case) => $case->value, self::cases()),
            array_map(fn (self $case) => $case->value, self::cases()),
        );
    }
}

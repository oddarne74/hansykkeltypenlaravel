<?php

namespace App\Enums;

enum BikeStatus: string
{
    case FOR_SALE = 'Til salgs';
    case RESERVED = 'Reservert';
    case SOLD = 'Solgt';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::FOR_SALE->value => 'Til salgs',
            self::RESERVED->value => 'Reservert',
            self::SOLD->value => 'Solgt',
        ];
    }
}

<?php

namespace App\Enums;

enum BikeType: string
{
    case Hybridsykkel = 'Hybridsykkel';
    case Terrengsykkel = 'Terrengsykkel';
    case Bysykkel = 'Bysykkel';
    case Racersykkel = 'Racersykkel';
    case Elsykkel = 'Elsykkel';
    case Barnesykkel = 'Barnesykkel';
    case Tursykkel = 'Tursykkel';
    case Gravelsykkel = 'Gravelsykkel';

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

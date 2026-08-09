<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case PENDING = 'Venter';
    case APPROVED = 'Godkjent';
    case DENIED = 'Avslått';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::PENDING->value => 'Venter',
            self::APPROVED->value => 'Godkjent',
            self::DENIED->value => 'Avslått',
        ];
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::DENIED => 'danger',
        };
    }
}

<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active    = 'active';
    case Pending   = 'pending';
    case Suspended = 'suspended';
    case Banned    = 'banned';

    public function label(): string
    {
        return match($this) {
            self::Active    => 'Active',
            self::Pending   => 'Pending Verification',
            self::Suspended => 'Suspended',
            self::Banned    => 'Banned',
        };
    }

    public function canLogin(): bool
    {
        return $this === self::Active;
    }
}

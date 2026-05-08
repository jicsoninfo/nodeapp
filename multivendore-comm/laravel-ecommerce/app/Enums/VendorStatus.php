<?php

namespace App\Enums;

enum VendorStatus: string
{
    case Pending   = 'pending';
    case Active    = 'active';
    case Suspended = 'suspended';
    case Rejected  = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pending Approval',
            self::Active    => 'Active',
            self::Suspended => 'Suspended',
            self::Rejected  => 'Rejected',
        };
    }

    public function canSell(): bool
    {
        return $this === self::Active;
    }
}

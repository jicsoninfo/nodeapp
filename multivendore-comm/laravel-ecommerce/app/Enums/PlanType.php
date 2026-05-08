<?php

namespace App\Enums;

enum PlanType: string
{
    case Free       = 'free';
    case Basic      = 'basic';
    case Pro        = 'pro';
    case Enterprise = 'enterprise';

    public function maxProducts(): int
    {
        return match($this) {
            self::Free       => 10,
            self::Basic      => 100,
            self::Pro        => 1000,
            self::Enterprise => PHP_INT_MAX,
        };
    }

    public function commissionDiscount(): float
    {
        return match($this) {
            self::Free       => 0.0,
            self::Basic      => 1.0,
            self::Pro        => 2.5,
            self::Enterprise => 5.0,
        };
    }
}

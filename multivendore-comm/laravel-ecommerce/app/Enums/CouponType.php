<?php

namespace App\Enums;

enum CouponType: string
{
    case Percent     = 'percent';
    case Fixed       = 'fixed';
    case FreeShipping = 'free_shipping';

    public function computeDiscount(float $orderTotal, float $value): float
    {
        return match($this) {
            self::Percent      => round($orderTotal * ($value / 100), 2),
            self::Fixed        => min($value, $orderTotal),
            self::FreeShipping => 0.0,
        };
    }
}

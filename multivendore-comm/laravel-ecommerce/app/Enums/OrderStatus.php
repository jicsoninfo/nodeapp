<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending    = 'pending';
    case Confirmed  = 'confirmed';
    case Processing = 'processing';
    case Shipped    = 'shipped';
    case Delivered  = 'delivered';
    case Cancelled  = 'cancelled';
    case Refunded   = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Pending',
            self::Confirmed  => 'Confirmed',
            self::Processing => 'Processing',
            self::Shipped    => 'Shipped',
            self::Delivered  => 'Delivered',
            self::Cancelled  => 'Cancelled',
            self::Refunded   => 'Refunded',
        };
    }

    public function canCancel(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed]);
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled, self::Refunded]);
    }

    /** Valid next states for state machine */
    public function transitions(): array
    {
        return match($this) {
            self::Pending    => [self::Confirmed, self::Cancelled],
            self::Confirmed  => [self::Processing, self::Cancelled],
            self::Processing => [self::Shipped, self::Cancelled],
            self::Shipped    => [self::Delivered],
            self::Delivered  => [self::Refunded],
            default          => [],
        };
    }
}

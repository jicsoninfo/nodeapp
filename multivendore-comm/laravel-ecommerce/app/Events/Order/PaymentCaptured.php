<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCaptured
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order   $order,
        public readonly Payment $payment,
    ) {}
}

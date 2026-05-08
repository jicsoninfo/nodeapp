<?php

namespace App\Contracts\Services;

use App\Models\Order;
use App\Models\Payment;

interface PaymentServiceInterface
{
    public function createIntent(Order $order): array;
    public function capture(Order $order, string $providerTxnId): Payment;
    public function refund(Payment $payment, float $amount): bool;
    public function handleWebhook(array $payload, string $provider): void;
}

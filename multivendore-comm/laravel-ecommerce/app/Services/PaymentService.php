<?php

namespace App\Services;

use App\Contracts\Services\PaymentServiceInterface;
use App\Enums\PaymentStatus;
use App\Events\Order\PaymentCaptured;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PaymentService implements PaymentServiceInterface
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createIntent(Order $order): array
    {
        $intent = $this->stripe->paymentIntents->create([
            'amount'   => (int) ($order->total_amount * 100),
            'currency' => strtolower($order->currency),
            'metadata' => ['order_id' => $order->id, 'order_number' => $order->order_number],
        ]);

        Payment::create([
            'order_id'   => $order->id,
            'method'     => 'card',
            'provider'   => 'stripe',
            'amount'     => $order->total_amount,
            'currency'   => $order->currency,
            'status'     => PaymentStatus::Pending,
            'meta'       => ['intent_id' => $intent->id],
        ]);

        return ['client_secret' => $intent->client_secret];
    }

    public function capture(Order $order, string $providerTxnId): Payment
    {
        $payment = $order->payment;
        $payment->update([
            'status'         => PaymentStatus::Captured,
            'provider_txn_id'=> $providerTxnId,
            'processed_at'   => now(),
        ]);

        event(new PaymentCaptured($order, $payment));

        return $payment;
    }

    public function refund(Payment $payment, float $amount): bool
    {
        try {
            $this->stripe->refunds->create([
                'payment_intent' => $payment->meta['intent_id'] ?? $payment->provider_txn_id,
                'amount'         => (int) ($amount * 100),
            ]);

            $payment->update(['status' => PaymentStatus::Refunded]);

            return true;
        } catch (\Exception $e) {
            Log::error('Refund failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function handleWebhook(array $payload, string $provider): void
    {
        match ($provider) {
            'stripe'    => $this->handleStripeWebhook($payload),
            'razorpay'  => $this->handleRazorpayWebhook($payload),
            default     => Log::warning("Unknown payment provider: {$provider}"),
        };
    }

    private function handleStripeWebhook(array $payload): void
    {
        $type = $payload['type'] ?? '';

        match ($type) {
            'payment_intent.succeeded' => $this->onStripeSuccess($payload['data']['object']),
            'payment_intent.payment_failed' => $this->onStripeFailure($payload['data']['object']),
            default => null,
        };
    }

    private function handleRazorpayWebhook(array $payload): void
    {
        // TODO: Implement Razorpay webhook handling
    }

    private function onStripeSuccess(array $intent): void
    {
        $orderId = $intent['metadata']['order_id'] ?? null;
        if (! $orderId) return;

        $order = Order::find($orderId);
        if ($order && $order->payment) {
            $this->capture($order, $intent['id']);
            $order->transitionTo(\App\Enums\OrderStatus::Confirmed);
        }
    }

    private function onStripeFailure(array $intent): void
    {
        $orderId = $intent['metadata']['order_id'] ?? null;
        $order   = Order::find($orderId);
        $order?->payment?->update(['status' => PaymentStatus::Failed]);
    }
}

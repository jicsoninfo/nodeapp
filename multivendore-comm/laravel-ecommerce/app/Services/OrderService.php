<?php

namespace App\Services;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Events\Order\OrderPlaced;
use App\Events\Order\OrderCancelled;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly CartServiceInterface     $cartService,
    ) {}

    public function placeOrder(User $user, Cart $cart, array $data): Order
    {
        return DB::transaction(function () use ($user, $cart, $data) {
            // Validate stock & lock variants
            foreach ($cart->items as $item) {
                $variant = $item->variant()->lockForUpdate()->first();
                throw_if(
                    $variant->stock_quantity < $item->quantity,
                    \App\Exceptions\InsufficientStockException::class,
                    "Insufficient stock for SKU: {$variant->sku}"
                );
            }

            // Compute totals
            $subtotal = $cart->items->sum(fn ($i) => $i->unit_price * $i->quantity);
            $discount = 0.0;

            if ($coupon = $cart->coupon ?? null) {
                throw_unless(
                    $coupon->isValid($subtotal),
                    \App\Exceptions\InvalidCouponException::class,
                    'This coupon is no longer valid.'
                );
                $discount = $coupon->computeDiscount($subtotal);
                $coupon->increment('used_count');
            }

            $shippingAmount = $this->calculateShipping($cart);
            $taxAmount      = $this->calculateTax($subtotal - $discount);
            $totalAmount    = $subtotal - $discount + $taxAmount + $shippingAmount;

            // Create order
            $order = $this->orders->create([
                'order' => [
                    'user_id'         => $user->id,
                    'address_id'      => $data['address_id'],
                    'coupon_id'       => $coupon?->id,
                    'order_number'    => Order::generateOrderNumber(),
                    'status'          => 'pending',
                    'currency'        => $data['currency'] ?? 'USD',
                    'subtotal'        => $subtotal,
                    'tax_amount'      => $taxAmount,
                    'shipping_amount' => $shippingAmount,
                    'discount_amount' => $discount,
                    'total_amount'    => $totalAmount,
                    'notes'           => $data['notes'] ?? null,
                    'placed_at'       => now(),
                ],
                'items' => $cart->items->map(fn ($item) => [
                    'vendor_id'          => $item->vendor_id,
                    'variant_id'         => $item->variant_id,
                    'quantity'           => $item->quantity,
                    'unit_price'         => $item->unit_price,
                    'tax_rate'           => 0.00,
                    'fulfillment_status' => 'pending',
                ])->toArray(),
            ]);

            // Decrement stock
            foreach ($cart->items as $item) {
                $item->variant->decrementStock($item->quantity);
            }

            // Clear cart
            $this->cartService->clear($cart);

            event(new OrderPlaced($order));

            return $order;
        });
    }

    public function cancelOrder(Order $order, string $reason = ''): Order
    {
        throw_unless(
            $order->status->canCancel(),
            \App\Exceptions\OrderCancellationException::class,
            "Order {$order->order_number} cannot be cancelled at this stage."
        );

        DB::transaction(function () use ($order) {
            $order->transitionTo(\App\Enums\OrderStatus::Cancelled);

            // Restore stock
            foreach ($order->items as $item) {
                $item->variant->incrementStock($item->quantity);
            }
        });

        event(new OrderCancelled($order, $reason));

        return $order->refresh();
    }

    public function processRefund(Order $order, float $amount): void
    {
        app(\App\Contracts\Services\PaymentServiceInterface::class)
            ->refund($order->payment, $amount);
    }

    private function calculateShipping(Cart $cart): float
    {
        // Stub — replace with real carrier integration
        return 0.00;
    }

    private function calculateTax(float $amount): float
    {
        // Stub — replace with tax API (TaxJar / Avalara)
        return round($amount * 0.09, 2);
    }
}

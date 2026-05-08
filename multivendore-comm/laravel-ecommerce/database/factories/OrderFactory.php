<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal       = fake()->randomFloat(2, 20, 2000);
        $taxAmount      = round($subtotal * 0.09, 2);
        $shippingAmount = fake()->boolean(60) ? 0 : 5.99;
        $discount       = fake()->boolean(20) ? round($subtotal * 0.10, 2) : 0;
        $total          = $subtotal + $taxAmount + $shippingAmount - $discount;

        return [
            'order_number'    => Order::generateOrderNumber(),
            'status'          => fake()->randomElement([
                OrderStatus::Delivered, OrderStatus::Delivered, OrderStatus::Delivered,
                OrderStatus::Shipped, OrderStatus::Processing, OrderStatus::Confirmed, OrderStatus::Pending,
            ]),
            'currency'        => fake()->randomElement(['USD', 'INR', 'EUR']),
            'subtotal'        => $subtotal,
            'tax_amount'      => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discount,
            'total_amount'    => max(0, $total),
            'placed_at'       => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            // Ensure user has a default address
            $address = $order->user->addresses()->where('is_default', true)->first()
                ?? Address::factory()->create(['user_id' => $order->user_id, 'is_default' => true]);

            $order->update(['address_id' => $address->id]);

            // Add 1-3 order items
            $variants = \App\Models\ProductVariant::inRandomOrder()->take(rand(1, 3))->get();
            foreach ($variants as $variant) {
                OrderItem::factory()->create([
                    'order_id'   => $order->id,
                    'variant_id' => $variant->id,
                    'vendor_id'  => $variant->product->vendor_id,
                ]);
            }

            // Payment record
            Payment::factory()->create(['order_id' => $order->id]);
        });
    }

    public function delivered(): static
    {
        return $this->state(fn () => ['status' => OrderStatus::Delivered]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => OrderStatus::Pending]);
    }
}

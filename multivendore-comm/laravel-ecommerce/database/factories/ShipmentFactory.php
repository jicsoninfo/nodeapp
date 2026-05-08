<?php
namespace Database\Factories;
use App\Models\OrderItem;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        $shippedAt = fake()->dateTimeBetween('-30 days', '-3 days');
        return [
            'order_item_id'  => OrderItem::factory(),
            'carrier'        => fake()->randomElement(['FedEx', 'UPS', 'DHL', 'BlueDart', 'DTDC']),
            'tracking_number'=> strtoupper(fake()->bothify('??########??')),
            'status'         => 'delivered',
            'shipped_at'     => $shippedAt,
            'estimated_at'   => (clone $shippedAt)->modify('+3 days'),
            'delivered_at'   => (clone $shippedAt)->modify('+2 days'),
        ];
    }

    public function inTransit(): static
    {
        return $this->state(fn () => ['status' => 'in_transit', 'delivered_at' => null]);
    }
}

<?php
namespace Database\Factories;
use App\Enums\PayoutStatus;
use App\Models\Vendor;
use App\Models\VendorPayout;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorPayoutFactory extends Factory
{
    protected $model = VendorPayout::class;

    public function definition(): array
    {
        $gross      = fake()->randomFloat(2, 100, 10000);
        $commission = round($gross * 0.15, 2);
        return [
            'vendor_id'           => Vendor::factory(),
            'gross_amount'        => $gross,
            'commission_deducted' => $commission,
            'net_amount'          => round($gross - $commission, 2),
            'currency'            => 'USD',
            'status'              => PayoutStatus::Paid,
            'reference_id'        => 'PAYOUT-' . strtoupper(fake()->bothify('????-######')),
            'paid_at'             => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => PayoutStatus::Pending, 'reference_id' => null, 'paid_at' => null]);
    }
}

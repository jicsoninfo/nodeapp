<?php
namespace App\Listeners\Order;
use App\Enums\OrderStatus;
use App\Events\Order\OrderStatusChanged;
use App\Jobs\ProcessVendorPayout;
use App\Models\Vendor;
use App\Models\VendorPayout;
use App\Services\VendorPayoutService;
use Illuminate\Contracts\Queue\ShouldQueue;

class TriggerVendorPayoutOnDelivery implements ShouldQueue
{
    public string $queue = 'payouts';
    public function __construct(private readonly VendorPayoutService $payoutService) {}

    public function handle(OrderStatusChanged $event): void
    {
        if ($event->current !== OrderStatus::Delivered) return;

        $vendorIds = $event->order->items->pluck('vendor_id')->unique();

        foreach ($vendorIds as $vendorId) {
            $vendor      = Vendor::find($vendorId);
            if (! $vendor) continue;

            $grossAmount = $event->order->items
                ->where('vendor_id', $vendorId)
                ->sum(fn ($i) => $i->unit_price * $i->quantity);

            $payout = $this->payoutService->createPayout($vendor, $grossAmount);

            // Actual bank transfer is batched monthly; just record the pending payout here
        }
    }
}

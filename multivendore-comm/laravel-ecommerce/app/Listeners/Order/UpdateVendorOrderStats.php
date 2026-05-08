<?php
namespace App\Listeners\Order;
use App\Events\Order\PaymentCaptured;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class UpdateVendorOrderStats implements ShouldQueue
{
    public string $queue = 'analytics';
    public function handle(PaymentCaptured $event): void
    {
        // Increment per-vendor order counts in a denormalized stats table (or cache)
        $vendorIds = $event->order->items->pluck('vendor_id')->unique();
        foreach ($vendorIds as $vendorId) {
            DB::table('vendor_profiles')
                ->where('vendor_id', $vendorId)
                ->increment('total_reviews', 0); // placeholder — extend with real stats columns
        }
    }
}

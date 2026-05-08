<?php
namespace App\Listeners\Order;
use App\Events\Order\OrderCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CancelRelatedShipments implements ShouldQueue
{
    public string $queue = 'default';
    public function handle(OrderCancelled $event): void
    {
        foreach ($event->order->items as $item) {
            $shipment = $item->shipment;
            if ($shipment && ! in_array($shipment->status, ['delivered','returned'])) {
                $shipment->update(['status' => 'returned']);
                Log::info("Shipment {$shipment->id} marked returned for cancelled order {$event->order->order_number}");
            }
        }
    }
}

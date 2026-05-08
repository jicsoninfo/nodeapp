<?php
namespace App\Listeners\Order;
use App\Events\Order\OrderCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;

class RestoreInventoryOnCancel implements ShouldQueue
{
    public string $queue = 'default';
    public function handle(OrderCancelled $event): void
    {
        foreach ($event->order->items as $item) {
            $item->variant?->incrementStock($item->quantity);
        }
    }
}

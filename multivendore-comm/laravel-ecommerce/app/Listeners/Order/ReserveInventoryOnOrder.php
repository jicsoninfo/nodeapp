<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPlaced;

class ReserveInventoryOnOrder
{
    public function handle(OrderPlaced $event): void
    {
        // Stock was already decremented in OrderService — log here for audit
        foreach ($event->order->items as $item) {
            activity()
                ->causedBy($event->order->user)
                ->on($item->variant)
                ->withProperties(['quantity_reserved' => $item->quantity, 'order_id' => $event->order->id])
                ->log('inventory_reserved');
        }
    }
}

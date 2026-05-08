<?php
namespace App\Observers;
use App\Enums\OrderStatus;
use App\Events\Order\OrderStatusChanged;
use App\Models\Order;

class OrderObserver
{
    public function updating(Order $order): void
    {
        if ($order->isDirty('status')) {
            $previous = OrderStatus::from($order->getOriginal('status'));
            $current  = $order->status;

            if ($previous !== $current) {
                event(new OrderStatusChanged($order, $previous, $current));
            }
        }
    }
}

<?php
namespace App\Listeners\Order;
use App\Events\Order\OrderStatusChanged;
use App\Jobs\SendPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderStatusPushNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public function handle(OrderStatusChanged $event): void
    {
        SendPushNotification::dispatch(
            $event->order->user,
            "Order Update",
            "Order {$event->order->order_number} is now {$event->current->label()}.",
            ['type' => 'order_status', 'order_id' => $event->order->id, 'status' => $event->current->value],
        );
    }
}

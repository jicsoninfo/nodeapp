<?php
namespace App\Listeners\Order;
use App\Events\Order\OrderCancelled;
use App\Notifications\Order\OrderCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderCancelledEmail implements ShouldQueue
{
    public string $queue = 'notifications';
    public function handle(OrderCancelled $event): void
    {
        $event->order->user->notify(
            new OrderCancelledNotification($event->order, $event->reason)
        );
    }
}

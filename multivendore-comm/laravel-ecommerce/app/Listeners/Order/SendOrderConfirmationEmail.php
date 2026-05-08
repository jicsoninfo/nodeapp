<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPlaced;
use App\Notifications\Order\OrderConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(OrderPlaced $event): void
    {
        $event->order->user->notify(new OrderConfirmedNotification($event->order));
    }
}

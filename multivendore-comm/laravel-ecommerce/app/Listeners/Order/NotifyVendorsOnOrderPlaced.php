<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPlaced;
use App\Notifications\Order\NewOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyVendorsOnOrderPlaced implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(OrderPlaced $event): void
    {
        $vendorIds = $event->order->items->pluck('vendor_id')->unique();
        $vendors   = \App\Models\Vendor::whereIn('id', $vendorIds)->with('owner')->get();

        foreach ($vendors as $vendor) {
            $vendor->owner->notify(new NewOrderNotification($event->order));
        }
    }
}

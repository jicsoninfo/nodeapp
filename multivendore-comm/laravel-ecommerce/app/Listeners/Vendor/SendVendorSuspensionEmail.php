<?php
namespace App\Listeners\Vendor;
use App\Events\Vendor\VendorSuspended;
use App\Notifications\Vendor\VendorSuspendedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVendorSuspensionEmail implements ShouldQueue
{
    public string $queue = 'notifications';
    public function handle(VendorSuspended $event): void
    {
        $event->vendor->owner->notify(
            new VendorSuspendedNotification($event->vendor, $event->reason)
        );
    }
}

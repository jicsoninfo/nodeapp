<?php

namespace App\Listeners\Vendor;

use App\Events\Vendor\VendorApproved;
use App\Notifications\Vendor\VendorApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVendorApprovalEmail implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(VendorApproved $event): void
    {
        $event->vendor->owner->notify(new VendorApprovedNotification($event->vendor));
    }
}

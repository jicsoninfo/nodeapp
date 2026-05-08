<?php
namespace App\Observers;
use App\Events\Vendor\VendorApproved;
use App\Events\Vendor\VendorSuspended;
use App\Enums\VendorStatus;
use App\Models\Vendor;

class VendorObserver
{
    public function updated(Vendor $vendor): void
    {
        if (! $vendor->wasChanged('status')) return;

        $newStatus = $vendor->status;

        if ($newStatus === VendorStatus::Active) {
            event(new VendorApproved($vendor));
        }

        if ($newStatus === VendorStatus::Suspended) {
            event(new VendorSuspended($vendor, 'Status updated by platform.'));
        }
    }
}

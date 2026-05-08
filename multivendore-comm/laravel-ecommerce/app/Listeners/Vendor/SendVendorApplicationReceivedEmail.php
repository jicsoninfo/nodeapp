<?php
namespace App\Listeners\Vendor;
use App\Events\Vendor\VendorApplicationReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;

class SendVendorApplicationReceivedEmail implements ShouldQueue
{
    public string $queue = 'notifications';
    public function handle(VendorApplicationReceived $event): void
    {
        $vendor = $event->vendor->load('owner');
        $vendor->owner->notify(new \App\Notifications\Vendor\VendorApplicationReceivedNotification($vendor));
    }
}

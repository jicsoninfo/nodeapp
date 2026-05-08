<?php
namespace App\Listeners\Vendor;
use App\Events\Vendor\VendorApplicationReceived;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyAdminOnVendorApplication implements ShouldQueue
{
    public string $queue = 'notifications';
    public function handle(VendorApplicationReceived $event): void
    {
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\Vendor\NewVendorApplicationAdminNotification($event->vendor));
        }
    }
}

<?php

namespace App\Notifications\Vendor;

use App\Jobs\SendPushNotification;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(public readonly Vendor $vendor) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("🎉 Your store has been approved!")
            ->greeting("Congratulations, {$notifiable->full_name}!")
            ->line("Your store **{$this->vendor->store_name}** has been approved and is now live on the marketplace.")
            ->line("You can now start listing products and accepting orders.")
            ->action('Go to Vendor Dashboard', url('/vendor/dashboard'))
            ->line("Here's what to do next:")
            ->line("1. Complete your store profile")
            ->line("2. Add your first products")
            ->line("3. Set up your return & shipping policies")
            ->line('Welcome to the marketplace family!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'vendor_approved',
            'channel'    => 'in_app',
            'title'      => 'Your store is approved! 🎉',
            'body'       => "Congratulations! {$this->vendor->store_name} is now live.",
            'vendor_id'  => $this->vendor->id,
            'store_name' => $this->vendor->store_name,
        ];
    }

    public function toPush(object $notifiable): void
    {
        SendPushNotification::dispatch(
            $notifiable,
            "Store Approved 🎉",
            "Your store {$this->vendor->store_name} is now live!",
            ['type' => 'vendor_approved', 'vendor_id' => $this->vendor->id],
        );
    }
}

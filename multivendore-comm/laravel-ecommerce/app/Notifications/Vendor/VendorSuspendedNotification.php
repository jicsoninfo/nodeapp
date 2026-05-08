<?php

namespace App\Notifications\Vendor;

use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorSuspendedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(
        public readonly Vendor $vendor,
        public readonly string $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Important: Your store has been suspended")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("We regret to inform you that your store **{$this->vendor->store_name}** has been temporarily suspended.")
            ->line("**Reason:** {$this->reason}")
            ->line("If you believe this is an error or would like to appeal, please contact our support team.")
            ->action('Contact Support', url('/support'))
            ->line('We take marketplace integrity seriously and aim to resolve disputes fairly.')
            ->salutation('Marketplace Trust & Safety Team');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'vendor_suspended',
            'channel'    => 'in_app',
            'title'      => 'Store suspended',
            'body'       => "Your store {$this->vendor->store_name} has been suspended. Reason: {$this->reason}",
            'vendor_id'  => $this->vendor->id,
        ];
    }
}

<?php
namespace App\Notifications\Vendor;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVendorApplicationAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public string $queue = 'notifications';
    public function __construct(public readonly Vendor $vendor) {}
    public function via(object $notifiable): array { return ['mail', 'database']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Vendor Application: {$this->vendor->store_name}")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("A new vendor has applied to sell on the marketplace.")
            ->line("**Store:** {$this->vendor->store_name}")
            ->line("**Owner:** {$this->vendor->owner?->email}")
            ->line("**Plan:** {$this->vendor->plan_type->value}")
            ->action('Review Application', url("/admin/vendors/{$this->vendor->id}"))
            ->salutation('Marketplace System');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'new_vendor_application',
            'channel'    => 'in_app',
            'title'      => "New vendor application: {$this->vendor->store_name}",
            'body'       => "Review and approve/reject from the admin dashboard.",
            'vendor_id'  => $this->vendor->id,
        ];
    }
}

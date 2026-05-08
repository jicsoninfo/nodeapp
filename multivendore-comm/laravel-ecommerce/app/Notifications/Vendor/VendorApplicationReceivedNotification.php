<?php
namespace App\Notifications\Vendor;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApplicationReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public string $queue = 'notifications';
    public function __construct(public readonly Vendor $vendor) {}
    public function via(object $notifiable): array { return ['mail', 'database']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We received your store application!')
            ->greeting("Hi {$notifiable->full_name},")
            ->line("Thank you for applying to sell on our marketplace.")
            ->line("**Store Name:** {$this->vendor->store_name}")
            ->line('Our team will review your application within 2–3 business days.')
            ->line('You will be notified by email once a decision is made.')
            ->action('Check Application Status', url('/onboarding/vendor/status'))
            ->salutation('The Marketplace Team');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'vendor_application_received',
            'channel'    => 'in_app',
            'title'      => 'Vendor application received',
            'body'       => "We received your application for {$this->vendor->store_name}. Review takes 2–3 days.",
            'vendor_id'  => $this->vendor->id,
        ];
    }
}

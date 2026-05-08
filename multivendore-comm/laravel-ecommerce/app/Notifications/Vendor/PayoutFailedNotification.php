<?php

namespace App\Notifications\Vendor;

use App\Models\VendorPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(
        public readonly VendorPayout $payout,
        public readonly string       $errorMessage = '',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Payout Failed — Action Required")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("Unfortunately, your payout of **{$this->payout->currency} " . number_format($this->payout->net_amount, 2) . "** could not be processed.")
            ->line("Please ensure your bank account details are correct and contact support if the problem persists.")
            ->action('Update Bank Details', url('/vendor/bank-accounts'))
            ->action('Contact Support', url('/support'))
            ->salutation('Marketplace Finance Team');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'      => 'payout_failed',
            'channel'   => 'in_app',
            'title'     => 'Payout failed — action required',
            'body'      => "Your payout of {$this->payout->currency} " . number_format($this->payout->net_amount, 2) . " failed. Please check your bank details.",
            'payout_id' => $this->payout->id,
        ];
    }
}

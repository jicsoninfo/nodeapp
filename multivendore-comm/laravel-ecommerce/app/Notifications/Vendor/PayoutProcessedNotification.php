<?php

namespace App\Notifications\Vendor;

use App\Jobs\SendPushNotification;
use App\Models\VendorPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(public readonly VendorPayout $payout) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->payout->net_amount, 2);

        return (new MailMessage)
            ->subject("Payout of {$this->payout->currency} {$amount} processed")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("Your payout has been processed successfully. 💰")
            ->line("**Amount:** {$this->payout->currency} {$amount}")
            ->line("**Reference:** {$this->payout->reference_id}")
            ->line("**Date:** " . $this->payout->paid_at->format('M d, Y'))
            ->line("Funds typically arrive within 1–3 business days depending on your bank.")
            ->action('View Payout Details', url('/vendor/payouts/' . $this->payout->id));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'      => 'payout_processed',
            'channel'   => 'in_app',
            'title'     => "Payout processed 💰",
            'body'      => "{$this->payout->currency} " . number_format($this->payout->net_amount, 2) . " has been sent to your bank.",
            'payout_id' => $this->payout->id,
            'amount'    => $this->payout->net_amount,
            'currency'  => $this->payout->currency,
        ];
    }

    public function toPush(object $notifiable): void
    {
        $amount = number_format($this->payout->net_amount, 2);

        SendPushNotification::dispatch(
            $notifiable,
            "Payout Sent 💰",
            "{$this->payout->currency} {$amount} has been transferred to your bank.",
            ['type' => 'payout_processed', 'payout_id' => $this->payout->id],
        );
    }
}

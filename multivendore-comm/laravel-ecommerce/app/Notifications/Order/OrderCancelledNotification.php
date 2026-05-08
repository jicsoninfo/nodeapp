<?php

namespace App\Notifications\Order;

use App\Jobs\SendPushNotification;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(
        public readonly Order  $order,
        public readonly string $reason = '',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Order Cancelled — {$this->order->order_number}")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("Your order **{$this->order->order_number}** has been cancelled.");

        if ($this->reason) {
            $mail->line("**Reason:** {$this->reason}");
        }

        if ($this->order->payment?->status->value === 'captured') {
            $mail->line("A full refund of **{$this->order->currency} " . number_format($this->order->total_amount, 2) . "** will be processed within 5–7 business days.");
        }

        return $mail
            ->action('Browse Products', url('/'))
            ->line('We hope to serve you again.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'order_cancelled',
            'channel'      => 'in_app',
            'title'        => "Order cancelled: {$this->order->order_number}",
            'body'         => $this->reason ?: 'Your order has been cancelled.',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }

    public function toPush(object $notifiable): void
    {
        SendPushNotification::dispatch(
            $notifiable,
            "Order Cancelled",
            "Order {$this->order->order_number} has been cancelled.",
            ['type' => 'order_cancelled', 'order_id' => $this->order->id],
        );
    }
}

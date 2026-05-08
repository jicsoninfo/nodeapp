<?php

namespace App\Notifications\Order;

use App\Jobs\SendPushNotification;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the buyer immediately after an order is placed & payment confirmed.
 * Channels: email + in_app + push (if device token exists).
 */
class OrderConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    // ── Email ─────────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;

        return (new MailMessage)
            ->subject("Order Confirmed — {$order->order_number}")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("Great news! Your order **{$order->order_number}** has been confirmed.")
            ->line("**Order Total:** {$order->currency} " . number_format($order->total_amount, 2))
            ->line("**Items:** {$order->items->count()} item(s)")
            ->action('View Order', url("/orders/{$order->id}"))
            ->line('We will notify you when your order is shipped.')
            ->salutation("Thank you for shopping with us!");
    }

    // ── Database (in-app) ─────────────────────────────────

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'order_confirmed',
            'channel'      => 'in_app',
            'title'        => "Order confirmed: {$this->order->order_number}",
            'body'         => "Your order of {$this->order->currency} " . number_format($this->order->total_amount, 2) . " is confirmed.",
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }

    // ── Push ──────────────────────────────────────────────

    public function toPush(object $notifiable): void
    {
        SendPushNotification::dispatch(
            $notifiable,
            "Order Confirmed ✅",
            "Your order {$this->order->order_number} has been confirmed.",
            ['type' => 'order_confirmed', 'order_id' => $this->order->id],
        );
    }
}

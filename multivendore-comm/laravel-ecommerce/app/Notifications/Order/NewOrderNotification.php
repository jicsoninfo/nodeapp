<?php

namespace App\Notifications\Order;

use App\Jobs\SendPushNotification;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the vendor owner when a new order contains their products.
 */
class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vendor     = $notifiable->vendor;
        $vendorItems = $this->order->items->where('vendor_id', $vendor?->id);
        $itemCount  = $vendorItems->count();
        $subtotal   = $vendorItems->sum(fn ($i) => $i->unit_price * $i->quantity);

        return (new MailMessage)
            ->subject("New Order Received — {$this->order->order_number}")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("You have a new order! 🛍️")
            ->line("**Order Number:** {$this->order->order_number}")
            ->line("**Items:** {$itemCount} item(s)")
            ->line("**Your Subtotal:** {$this->order->currency} " . number_format($subtotal, 2))
            ->action('Process Order', url("/vendor/orders/{$this->order->id}"))
            ->line('Please process this order as soon as possible to maintain your seller rating.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'new_order',
            'channel'      => 'in_app',
            'title'        => "New order: {$this->order->order_number}",
            'body'         => "You have a new order waiting to be processed.",
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }

    public function toPush(object $notifiable): void
    {
        SendPushNotification::dispatch(
            $notifiable,
            "New Order 🛍️",
            "Order {$this->order->order_number} needs processing.",
            ['type' => 'new_order', 'order_id' => $this->order->id],
        );
    }
}

<?php

namespace App\Notifications\Order;

use App\Jobs\SendPushNotification;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when a vendor marks an order item as shipped.
 * Includes carrier & tracking info.
 */
class OrderShippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(
        public readonly Order    $order,
        public readonly Shipment $shipment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Your order is on its way! — {$this->order->order_number}")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("Your order **{$this->order->order_number}** has been shipped! 🚚");

        if ($this->shipment->carrier && $this->shipment->tracking_number) {
            $mail->line("**Carrier:** {$this->shipment->carrier}")
                 ->line("**Tracking Number:** {$this->shipment->tracking_number}");
        }

        if ($this->shipment->estimated_at) {
            $mail->line("**Estimated Delivery:** " . $this->shipment->estimated_at->format('M d, Y'));
        }

        return $mail
            ->action('Track Your Order', url("/orders/{$this->order->id}/tracking"))
            ->line('Thank you for shopping with us!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'            => 'order_shipped',
            'channel'         => 'in_app',
            'title'           => "Order shipped: {$this->order->order_number}",
            'body'            => "Your order is on its way! Tracking: {$this->shipment->tracking_number}",
            'order_id'        => $this->order->id,
            'order_number'    => $this->order->order_number,
            'tracking_number' => $this->shipment->tracking_number,
            'carrier'         => $this->shipment->carrier,
        ];
    }

    public function toPush(object $notifiable): void
    {
        SendPushNotification::dispatch(
            $notifiable,
            "Your Order Shipped 📦",
            "Order {$this->order->order_number} is on its way. Tracking: {$this->shipment->tracking_number}",
            ['type' => 'order_shipped', 'order_id' => $this->order->id],
        );
    }
}

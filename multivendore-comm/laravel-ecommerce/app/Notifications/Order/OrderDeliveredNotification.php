<?php

namespace App\Notifications\Order;

use App\Jobs\SendPushNotification;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when an order is marked as delivered.
 * Includes a prompt to leave a review.
 */
class OrderDeliveredNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject("Your order has been delivered! — {$this->order->order_number}")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("Your order **{$this->order->order_number}** has been delivered. We hope you love it! 🎉")
            ->action('Leave a Review', url("/orders/{$this->order->id}/review"))
            ->line('Your feedback helps other shoppers make great decisions.')
            ->line('If anything is wrong, please contact our support team within 48 hours.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'order_delivered',
            'channel'      => 'in_app',
            'title'        => 'Your order has been delivered!',
            'body'         => "Order {$this->order->order_number} is delivered. Share your thoughts — leave a review!",
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }

    public function toPush(object $notifiable): void
    {
        SendPushNotification::dispatch(
            $notifiable,
            "Delivered! 🎉",
            "Your order {$this->order->order_number} has arrived. How was it?",
            ['type' => 'order_delivered', 'order_id' => $this->order->id],
        );
    }
}

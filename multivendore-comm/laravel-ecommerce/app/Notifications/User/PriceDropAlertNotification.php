<?php

namespace App\Notifications\User;

use App\Jobs\SendPushNotification;
use App\Models\ProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when a wishlisted product drops in price.
 */
class PriceDropAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(
        public readonly ProductVariant $variant,
        public readonly float          $oldPrice,
        public readonly float          $newPrice,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $product  = $this->variant->product->getTranslation('en');
        $saving   = number_format($this->oldPrice - $this->newPrice, 2);
        $currency = $this->variant->currency;

        return (new MailMessage)
            ->subject("Price drop on {$product?->name}!")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("Great news! An item on your wishlist just dropped in price. 🎉")
            ->line("**{$product?->name}**")
            ->line("~~{$currency} " . number_format($this->oldPrice, 2) . "~~ → **{$currency} " . number_format($this->newPrice, 2) . "**")
            ->line("You save: {$currency} {$saving}")
            ->action('Buy Now', url("/products/{$this->variant->product_id}"))
            ->line('This price may not last long — grab it while you can!');
    }

    public function toDatabase(object $notifiable): array
    {
        $product = $this->variant->product->getTranslation('en');

        return [
            'type'       => 'price_drop',
            'channel'    => 'in_app',
            'title'      => "Price drop on {$product?->name}!",
            'body'       => "Now only {$this->variant->currency} " . number_format($this->newPrice, 2) . " — save " . number_format($this->oldPrice - $this->newPrice, 2),
            'variant_id' => $this->variant->id,
            'product_id' => $this->variant->product_id,
            'old_price'  => $this->oldPrice,
            'new_price'  => $this->newPrice,
        ];
    }

    public function toPush(object $notifiable): void
    {
        $product = $this->variant->product->getTranslation('en');

        SendPushNotification::dispatch(
            $notifiable,
            "Price Drop Alert! 🔥",
            "{$product?->name} is now {$this->variant->currency} " . number_format($this->newPrice, 2),
            ['type' => 'price_drop', 'product_id' => $this->variant->product_id],
        );
    }
}

<?php

namespace App\Notifications\Vendor;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $queue = 'notifications';

    public function __construct(public readonly Review $review) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $stars   = str_repeat('⭐', $this->review->rating);
        $product = $this->review->product->getTranslation('en');

        return (new MailMessage)
            ->subject("New {$this->review->rating}-star review on {$product?->name}")
            ->greeting("Hi {$notifiable->full_name},")
            ->line("A customer just left a review on one of your products.")
            ->line("**Product:** {$product?->name}")
            ->line("**Rating:** {$stars} ({$this->review->rating}/5)")
            ->line("**Review:** \"{$this->review->title}\"")
            ->action('View Review', url("/vendor/reviews/{$this->review->id}"))
            ->line('You can reply to this review from your vendor dashboard.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'new_review',
            'channel'    => 'in_app',
            'title'      => "New {$this->review->rating}★ review",
            'body'       => "\"{$this->review->title}\"",
            'review_id'  => $this->review->id,
            'product_id' => $this->review->product_id,
            'rating'     => $this->review->rating,
        ];
    }
}

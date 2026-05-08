<?php
namespace App\Listeners\Product;
use App\Events\Product\ReviewSubmitted;
use App\Notifications\Vendor\NewReviewNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyVendorOnNewReview implements ShouldQueue
{
    public string $queue = 'notifications';
    public function handle(ReviewSubmitted $event): void
    {
        $vendor = $event->review->product->vendor;
        $vendor?->owner?->notify(new NewReviewNotification($event->review));
    }
}

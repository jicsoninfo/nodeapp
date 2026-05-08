<?php
namespace App\Observers;
use App\Events\Product\ReviewSubmitted;
use App\Models\Review;

class ReviewObserver
{
    public function created(Review $review): void
    {
        if ($review->status === 'approved') {
            event(new ReviewSubmitted($review));
        }
    }

    public function updated(Review $review): void
    {
        // Fire when a pending review is approved by a moderator
        if ($review->wasChanged('status') && $review->status === 'approved') {
            event(new ReviewSubmitted($review));
        }
    }
}

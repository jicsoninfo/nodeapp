<?php
namespace App\Listeners\Product;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Events\Product\ReviewSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateProductRatingOnReview implements ShouldQueue
{
    public string $queue = 'analytics';
    public function __construct(private readonly ProductRepositoryInterface $products) {}
    public function handle(ReviewSubmitted $event): void
    {
        $this->products->updateRating($event->review->product_id);
    }
}

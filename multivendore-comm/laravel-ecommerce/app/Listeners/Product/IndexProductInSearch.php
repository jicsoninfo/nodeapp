<?php
namespace App\Listeners\Product;
use App\Events\Product\ProductCreated;
use App\Events\Product\ProductApproved;
use App\Jobs\UpdateProductSearchIndex;
use Illuminate\Contracts\Queue\ShouldQueue;

class IndexProductInSearch implements ShouldQueue
{
    public string $queue = 'search';
    public function handle(ProductCreated|ProductApproved $event): void
    {
        UpdateProductSearchIndex::dispatch($event->product);
    }
}

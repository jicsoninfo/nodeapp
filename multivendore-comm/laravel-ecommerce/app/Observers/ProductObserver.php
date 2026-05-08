<?php
namespace App\Observers;
use App\Events\Product\ProductCreated;
use App\Events\Product\ProductApproved;
use App\Enums\ProductStatus;
use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        event(new ProductCreated($product));
    }

    public function updated(Product $product): void
    {
        // Fire ProductApproved when status flips to active
        if ($product->wasChanged('status') &&
            $product->status === ProductStatus::Active &&
            $product->getOriginal('status') !== ProductStatus::Active->value) {
            event(new ProductApproved($product));
        }
    }

    public function deleting(Product $product): void
    {
        // Unsearchable before soft-delete so Algolia index is clean
        $product->unsearchable();
    }
}

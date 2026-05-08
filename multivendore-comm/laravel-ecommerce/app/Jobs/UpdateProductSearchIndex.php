<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProductSearchIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'search';
    public int    $tries = 3;

    public function __construct(public readonly Product $product) {}

    public function handle(): void
    {
        if ($this->product->status->isVisible()) {
            $this->product->searchable();
        } else {
            $this->product->unsearchable();
        }
    }
}

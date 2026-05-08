<?php

namespace App\Jobs;

use App\Models\ProductView;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Asynchronously records a product page view for analytics.
 * Dispatched from the public ProductController on every show() hit.
 * Runs on the 'analytics' queue so it never delays API responses.
 */
class RecordProductView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries    = 3;
    public int $timeout  = 30;
    public string $queue = 'analytics';

    public function __construct(
        public readonly string  $productId,
        public readonly ?string $userId,
        public readonly ?string $sessionId,
        public readonly ?string $referrerType,
    ) {}

    public function handle(): void
    {
        // Skip bots & health-check pings
        if (! $this->productId) return;

        // De-duplicate: one view per user/session per product per hour
        $alreadyViewed = ProductView::where('product_id', $this->productId)
            ->where(function ($q) {
                if ($this->userId) {
                    $q->where('user_id', $this->userId);
                } else {
                    $q->where('session_id', $this->sessionId);
                }
            })
            ->where('viewed_at', '>=', now()->subHour())
            ->exists();

        if ($alreadyViewed) return;

        ProductView::create([
            'product_id'   => $this->productId,
            'user_id'      => $this->userId,
            'session_id'   => $this->sessionId,
            'referrer_type'=> $this->referrerType,
            'viewed_at'    => now(),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::warning('RecordProductView failed', [
            'product_id' => $this->productId,
            'error'      => $e->getMessage(),
        ]);
    }
}

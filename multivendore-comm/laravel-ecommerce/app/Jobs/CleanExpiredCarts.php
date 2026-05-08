<?php

namespace App\Jobs;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanExpiredCarts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'default';

    public function handle(): void
    {
        $deleted = Cart::where('expires_at', '<', now())
            ->whereDoesntHave('user') // only guest carts
            ->each(fn ($cart) => $cart->delete());

        Log::info('CleanExpiredCarts: cleaned guest carts', ['count' => $deleted]);
    }
}

<?php

namespace App\Jobs;

use App\Models\ExchangeRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'default';
    public int    $tries = 3;

    public function handle(): void
    {
        $base     = config('marketplace.currency', 'USD');
        $apiKey   = config('services.exchange_rates.api_key');
        $endpoint = "https://api.exchangerate.host/latest?base={$base}";

        $response = Http::timeout(10)->get($endpoint);

        if ($response->failed()) {
            Log::error('SyncExchangeRates: API request failed', ['status' => $response->status()]);
            return;
        }

        $rates = $response->json('rates', []);

        foreach ($rates as $currency => $rate) {
            ExchangeRate::create([
                'from_currency' => $base,
                'to_currency'   => $currency,
                'rate'          => $rate,
                'fetched_at'    => now(),
            ]);
        }

        Log::info('Exchange rates synced', ['base' => $base, 'count' => count($rates)]);
    }
}

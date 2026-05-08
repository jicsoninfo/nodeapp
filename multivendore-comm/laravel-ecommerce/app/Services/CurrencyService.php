<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) return $amount;

        $rate = $this->getRate($from, $to);

        return round($amount * $rate, 2);
    }

    public function getRate(string $from, string $to): float
    {
        $cacheKey = "exchange_rate:{$from}:{$to}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($from, $to) {
            $rate = ExchangeRate::where('from_currency', $from)
                ->where('to_currency', $to)
                ->latest('fetched_at')
                ->first();

            return $rate?->rate ?? 1.0;
        });
    }

    public function formatAmount(float $amount, string $currency): string
    {
        $symbols = [
            'USD' => '$', 'EUR' => '€', 'INR' => '₹',
            'GBP' => '£', 'JPY' => '¥', 'AED' => 'د.إ',
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';

        return $symbol . number_format($amount, 2);
    }
}

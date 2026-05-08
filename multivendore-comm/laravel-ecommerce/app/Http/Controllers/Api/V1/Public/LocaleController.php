<?php
namespace App\Http\Controllers\Api\V1\Public;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Language;
use Illuminate\Http\JsonResponse;

class LocaleController extends Controller
{
    public function languages(): JsonResponse
    {
        return response()->json(['data' => Language::active()->get()]);
    }

    public function currencies(): JsonResponse
    {
        return response()->json(['data' => Currency::active()->get()]);
    }

    public function exchangeRates(): JsonResponse
    {
        $rates = ExchangeRate::where('from_currency','USD')->latest('fetched_at')->get()->unique('to_currency');
        return response()->json(['data' => $rates]);
    }

    public function rate(string $from, string $to): JsonResponse
    {
        $rate = ExchangeRate::where('from_currency', strtoupper($from))
            ->where('to_currency', strtoupper($to))
            ->latest('fetched_at')->firstOrFail();
        return response()->json(['data' => ['from' => $from, 'to' => $to, 'rate' => $rate->rate, 'fetched_at' => $rate->fetched_at]]);
    }
}

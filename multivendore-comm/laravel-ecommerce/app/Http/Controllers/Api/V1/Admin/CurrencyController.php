<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Jobs\SyncExchangeRates;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(): JsonResponse { return response()->json(['data' => Currency::all()]); }
    public function store(Request $request): JsonResponse { return response()->json(['data' => Currency::create($request->validate(['code'=>'required|size:3|unique:currencies,code','name'=>'required|max:100','symbol'=>'required|max:10','decimal_places'=>'integer|min:0']))], 201); }
    public function show(Currency $currency): JsonResponse { return response()->json(['data' => $currency]); }
    public function update(Request $request, Currency $currency): JsonResponse { $currency->update($request->validate(['is_active'=>'boolean'])); return response()->json(['data' => $currency->fresh()]); }
    public function destroy(Currency $currency): JsonResponse { $currency->delete(); return response()->json(['message' => 'Deleted.']); }
    public function syncRates(): JsonResponse { SyncExchangeRates::dispatch(); return response()->json(['message' => 'Exchange rate sync dispatched.']); }
}

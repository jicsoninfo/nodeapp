<?php
namespace App\Http\Controllers\Api\V1\Buyer;
use App\Contracts\Services\PaymentServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentServiceInterface $payments) {}

    public function createIntent(Request $request): JsonResponse
    {
        $request->validate(['order_id' => 'required|uuid|exists:orders,id']);
        $order = Order::where('user_id', $request->user()->id)->findOrFail($request->order_id);
        $intent = $this->payments->createIntent($order);
        return response()->json(['data' => $intent]);
    }

    public function confirm(Request $request): JsonResponse
    {
        $request->validate(['order_id' => 'required|uuid', 'provider_txn_id' => 'required|string']);
        $order = Order::where('user_id', $request->user()->id)->findOrFail($request->order_id);
        $payment = $this->payments->capture($order, $request->provider_txn_id);
        return response()->json(['data' => $payment, 'message' => 'Payment confirmed.']);
    }

    public function methods(): JsonResponse
    {
        return response()->json(['data' => ['card', 'upi', 'netbanking', 'wallet', 'cod', 'bnpl']]);
    }
}

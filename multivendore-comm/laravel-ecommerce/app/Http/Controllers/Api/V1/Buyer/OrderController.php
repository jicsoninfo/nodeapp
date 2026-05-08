<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PlaceOrderRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderServiceInterface   $orderService,
        private readonly CartServiceInterface    $cartService,
        private readonly PaymentServiceInterface $paymentService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items', 'payment'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('placed_at')
            ->paginate(15);

        return response()->json(['data' => OrderResource::collection($orders)]);
    }

    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $cart  = $this->cartService->getOrCreate($request->user());
        abort_if($cart->items->isEmpty(), 422, 'Your cart is empty.');

        $order = $this->orderService->placeOrder($request->user(), $cart, $request->validated());

        $paymentIntent = $this->paymentService->createIntent($order);

        return response()->json([
            'data'           => new OrderResource($order->load(['items', 'payment'])),
            'payment_intent' => $paymentIntent,
        ], 201);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        return response()->json([
            'data' => new OrderResource($order->load(['items.variant', 'items.shipment', 'payment', 'address'])),
        ]);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $request->validate(['reason' => 'nullable|string|max:500']);

        $order = $this->orderService->cancelOrder($order, $request->reason ?? '');

        return response()->json([
            'data'    => new OrderResource($order->load('items')),
            'message' => 'Order cancelled successfully.',
        ]);
    }
}

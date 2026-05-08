<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['user','items'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->from,   fn ($q) => $q->whereDate('placed_at', '>=', $request->from))
            ->when($request->to,     fn ($q) => $q->whereDate('placed_at', '<=', $request->to))
            ->latest('placed_at')->paginate(25);
        return response()->json(['data' => OrderResource::collection($orders)]);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json(['data' => new OrderResource($order->load(['items.variant','items.shipment','payment','user','address']))]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $order->update(['status' => $request->status]);
        return response()->json(['data' => new OrderResource($order->fresh()), 'message' => 'Order status updated.']);
    }

    public function refund(Request $request, Order $order): JsonResponse
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);
        app(\App\Contracts\Services\PaymentServiceInterface::class)->refund($order->payment, $request->amount);
        return response()->json(['message' => 'Refund initiated.']);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () {
            echo "Order #,User,Status,Total,Date\n";
            Order::with('user')->chunk(100, function ($orders) {
                foreach ($orders as $o) {
                    echo "{$o->order_number},{$o->user->email},{$o->status->value},{$o->total_amount},{$o->placed_at}\n";
                }
            });
        }, 'orders-export.csv', ['Content-Type' => 'text/csv']);
    }
}

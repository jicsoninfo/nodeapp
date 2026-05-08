<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vendorId = $request->user()->vendor->id;
        $orders   = Order::whereHas('items', fn ($q) => $q->where('vendor_id', $vendorId))
            ->with(['items' => fn ($q) => $q->where('vendor_id', $vendorId), 'user'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('placed_at')->paginate(20);
        return response()->json(['data' => $orders]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorize('fulfill', $order);
        return response()->json(['data' => $order->load(['items.variant', 'items.shipment', 'user', 'address'])]);
    }

    public function markFulfilled(Request $request, OrderItem $orderItem): JsonResponse
    {
        abort_if($orderItem->vendor_id !== $request->user()->vendor->id, 403);
        $orderItem->update(['fulfillment_status' => 'shipped']);
        return response()->json(['message' => 'Item marked as shipped.']);
    }

    public function addShipment(Request $request, OrderItem $orderItem): JsonResponse
    {
        abort_if($orderItem->vendor_id !== $request->user()->vendor->id, 403);
        $data = $request->validate([
            'carrier'         => 'required|string|max:100',
            'tracking_number' => 'nullable|string|max:200',
            'estimated_at'    => 'nullable|date',
        ]);

        $shipment = Shipment::updateOrCreate(
            ['order_item_id' => $orderItem->id],
            array_merge($data, ['status' => 'in_transit', 'shipped_at' => now()])
        );

        $orderItem->update(['fulfillment_status' => 'shipped']);

        return response()->json(['data' => $shipment, 'message' => 'Shipment added.']);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $vendorId = $request->user()->vendor->id;
        $orders   = Order::whereHas('items', fn ($q) => $q->where('vendor_id', $vendorId))->with('items', 'user')->get();
        return response()->streamDownload(function () use ($orders) {
            echo "Order #,Status,Total,Customer,Date\n";
            foreach ($orders as $o) {
                echo "{$o->order_number},{$o->status->value},{$o->total_amount},{$o->user->email},{$o->placed_at}\n";
            }
        }, 'orders.csv', ['Content-Type' => 'text/csv']);
    }
}

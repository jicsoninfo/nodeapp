<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $vendor = $request->user()->vendor;
        abort_unless($vendor, 403, 'Not a vendor account.');

        $stats = [
            'total_products'  => Product::where('vendor_id', $vendor->id)->count(),
            'active_products' => Product::where('vendor_id', $vendor->id)->where('status', 'active')->count(),
            'total_orders'    => DB::table('order_items')->where('vendor_id', $vendor->id)->distinct('order_id')->count('order_id'),
            'pending_orders'  => DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.vendor_id', $vendor->id)
                ->where('orders.status', 'confirmed')
                ->count(),
            'revenue_this_month' => DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.vendor_id', $vendor->id)
                ->where('orders.status', 'delivered')
                ->whereMonth('orders.placed_at', now()->month)
                ->whereYear('orders.placed_at', now()->year)
                ->sum(DB::raw('order_items.unit_price * order_items.quantity')),
            'avg_rating'      => $vendor->profile?->avg_rating ?? 0,
            'total_reviews'   => $vendor->profile?->total_reviews ?? 0,
        ];

        $recentOrders = Order::whereHas('items', fn ($q) => $q->where('vendor_id', $vendor->id))
            ->with(['items' => fn ($q) => $q->where('vendor_id', $vendor->id), 'user'])
            ->latest('placed_at')
            ->take(5)
            ->get();

        return response()->json([
            'data' => [
                'stats'        => $stats,
                'recent_orders'=> $recentOrders,
            ],
        ]);
    }
}

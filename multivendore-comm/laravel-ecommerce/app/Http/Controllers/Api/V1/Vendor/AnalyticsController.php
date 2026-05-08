<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $vendorId = $request->user()->vendor->id;
        return response()->json(['data' => [
            'total_revenue'   => $this->revenue($vendorId, 12),
            'total_orders'    => DB::table('order_items')->where('vendor_id', $vendorId)->distinct('order_id')->count('order_id'),
            'total_products'  => DB::table('products')->where('vendor_id', $vendorId)->where('status', 'active')->count(),
            'avg_order_value' => DB::table('order_items')->join('orders','orders.id','=','order_items.order_id')->where('order_items.vendor_id', $vendorId)->avg(DB::raw('order_items.unit_price * order_items.quantity')),
        ]]);
    }

    public function revenue(Request $request): JsonResponse
    {
        $vendorId = $request->user()->vendor->id;
        $months   = $request->get('months', 6);
        $data     = DB::table('order_items')
            ->join('orders','orders.id','=','order_items.order_id')
            ->where('order_items.vendor_id', $vendorId)
            ->where('orders.status', 'delivered')
            ->where('orders.placed_at', '>=', now()->subMonths($months))
            ->selectRaw('DATE_FORMAT(orders.placed_at, "%Y-%m") as month, SUM(order_items.unit_price * order_items.quantity) as revenue')
            ->groupBy('month')->orderBy('month')->get();
        return response()->json(['data' => $data]);
    }

    public function orders(Request $request): JsonResponse
    {
        $vendorId = $request->user()->vendor->id;
        $data     = DB::table('order_items')
            ->join('orders','orders.id','=','order_items.order_id')
            ->where('order_items.vendor_id', $vendorId)
            ->where('orders.placed_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(orders.placed_at, "%Y-%m") as month, COUNT(DISTINCT orders.id) as orders_count')
            ->groupBy('month')->orderBy('month')->get();
        return response()->json(['data' => $data]);
    }

    public function products(Request $request): JsonResponse
    {
        $vendorId = $request->user()->vendor->id;
        $data     = DB::table('order_items')
            ->join('product_variants','product_variants.id','=','order_items.variant_id')
            ->join('products','products.id','=','product_variants.product_id')
            ->join('product_translations','product_translations.product_id','=','products.id')
            ->where('order_items.vendor_id', $vendorId)
            ->where('product_translations.lang_code', 'en')
            ->selectRaw('products.id, product_translations.name, SUM(order_items.quantity) as units_sold, SUM(order_items.unit_price * order_items.quantity) as revenue')
            ->groupBy('products.id','product_translations.name')
            ->orderByDesc('revenue')->take(10)->get();
        return response()->json(['data' => $data]);
    }

    public function customers(Request $request): JsonResponse
    {
        $vendorId = $request->user()->vendor->id;
        $data     = DB::table('order_items')
            ->join('orders','orders.id','=','order_items.order_id')
            ->join('users','users.id','=','orders.user_id')
            ->where('order_items.vendor_id', $vendorId)
            ->selectRaw('users.email, COUNT(DISTINCT orders.id) as order_count, SUM(order_items.unit_price * order_items.quantity) as total_spent')
            ->groupBy('users.id','users.email')
            ->orderByDesc('total_spent')->take(10)->get();
        return response()->json(['data' => $data]);
    }

    private function revenue(string $vendorId, int $months): float
    {
        return (float) DB::table('order_items')
            ->join('orders','orders.id','=','order_items.order_id')
            ->where('order_items.vendor_id', $vendorId)
            ->where('orders.status', 'delivered')
            ->where('orders.placed_at', '>=', now()->subMonths($months))
            ->sum(DB::raw('order_items.unit_price * order_items.quantity'));
    }
}

<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['data' => [
            'users'           => DB::table('users')->count(),
            'vendors'         => DB::table('vendors')->where('status','active')->count(),
            'pending_vendors' => DB::table('vendors')->where('status','pending')->count(),
            'products'        => DB::table('products')->where('status','active')->count(),
            'orders_today'    => DB::table('orders')->whereDate('placed_at', today())->count(),
            'revenue_today'   => DB::table('orders')->whereDate('placed_at', today())->where('status','delivered')->sum('total_amount'),
            'revenue_month'   => DB::table('orders')->whereMonth('placed_at', now()->month)->whereYear('placed_at', now()->year)->where('status','delivered')->sum('total_amount'),
            'pending_reviews' => DB::table('reviews')->where('status','pending')->count(),
        ]]);
    }
}

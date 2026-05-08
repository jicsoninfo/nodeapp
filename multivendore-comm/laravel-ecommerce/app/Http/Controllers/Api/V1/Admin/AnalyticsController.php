<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function overview(): JsonResponse
    {
        return response()->json(['data' => [
            'users_total'      => DB::table('users')->count(),
            'users_this_month' => DB::table('users')->whereMonth('created_at', now()->month)->count(),
            'vendors_active'   => DB::table('vendors')->where('status','active')->count(),
            'orders_total'     => DB::table('orders')->count(),
            'orders_this_month'=> DB::table('orders')->whereMonth('placed_at', now()->month)->count(),
            'revenue_total'    => DB::table('orders')->where('status','delivered')->sum('total_amount'),
            'revenue_month'    => DB::table('orders')->where('status','delivered')->whereMonth('placed_at', now()->month)->sum('total_amount'),
            'products_active'  => DB::table('products')->where('status','active')->count(),
        ]]);
    }

    public function revenue(): JsonResponse
    {
        $data = DB::table('orders')->where('status','delivered')->where('placed_at','>=',now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(placed_at, "%Y-%m") as month, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('month')->orderBy('month')->get();
        return response()->json(['data' => $data]);
    }

    public function users(): JsonResponse
    {
        $data = DB::table('users')->where('created_at','>=',now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as registrations')
            ->groupBy('month')->orderBy('month')->get();
        return response()->json(['data' => $data]);
    }

    public function vendors(): JsonResponse
    {
        $data = DB::table('vendors')->selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
        return response()->json(['data' => $data]);
    }

    public function products(): JsonResponse
    {
        $data = DB::table('products')->selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
        return response()->json(['data' => $data]);
    }

    public function search(): JsonResponse
    {
        $data = DB::table('search_queries')->where('searched_at','>=',now()->subDays(30))
            ->selectRaw('query, COUNT(*) as count')->groupBy('query')->orderByDesc('count')->take(20)->get();
        return response()->json(['data' => $data]);
    }
}

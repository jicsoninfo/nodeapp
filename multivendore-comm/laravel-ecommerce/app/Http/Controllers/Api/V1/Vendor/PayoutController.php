<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Models\VendorPayout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $payouts = VendorPayout::where('vendor_id', $request->user()->vendor->id)->latest()->paginate(20);
        return response()->json(['data' => $payouts]);
    }

    public function pending(Request $request): JsonResponse
    {
        $payouts = VendorPayout::where('vendor_id', $request->user()->vendor->id)->pending()->get();
        return response()->json(['data' => $payouts, 'total_pending' => $payouts->sum('net_amount')]);
    }

    public function show(Request $request, VendorPayout $payout): JsonResponse
    {
        abort_if($payout->vendor_id !== $request->user()->vendor->id, 403);
        return response()->json(['data' => $payout]);
    }
}

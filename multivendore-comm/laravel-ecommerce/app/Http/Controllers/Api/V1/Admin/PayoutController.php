<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessVendorPayout;
use App\Models\VendorPayout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class PayoutController extends Controller
{
    public function index(Request $request): JsonResponse { return response()->json(['data' => VendorPayout::with('vendor')->when($request->status,fn($q)=>$q->where('status',$request->status))->latest()->paginate(25)]); }
    public function process(VendorPayout $payout): JsonResponse { ProcessVendorPayout::dispatch($payout); return response()->json(['message' => 'Payout dispatched.']); }
    public function processBatch(Request $request): JsonResponse
    {
        $payouts = VendorPayout::pending()->get();
        $jobs    = $payouts->map(fn ($p) => new ProcessVendorPayout($p))->toArray();
        Bus::batch($jobs)->name('Admin Payout Batch')->allowFailures()->dispatch();
        return response()->json(['message' => count($jobs) . ' payouts dispatched.']);
    }
}

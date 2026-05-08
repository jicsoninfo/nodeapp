<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => Coupon::where('vendor_id', $request->user()->vendor->id)->latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => 'required|string|unique:coupons,code|max:50', 'type' => 'required|in:percent,fixed,free_shipping', 'value' => 'required|numeric|min:0', 'min_order' => 'nullable|numeric|min:0', 'usage_limit' => 'nullable|integer|min:1', 'expires_at' => 'nullable|date|after:today']);
        $coupon = Coupon::create(array_merge($data, ['vendor_id' => $request->user()->vendor->id]));
        return response()->json(['data' => $coupon], 201);
    }

    public function show(Request $request, Coupon $coupon): JsonResponse
    {
        $this->authorize('update', $coupon);
        return response()->json(['data' => $coupon]);
    }

    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $this->authorize('update', $coupon);
        $coupon->update($request->validate(['value' => 'sometimes|numeric|min:0', 'min_order' => 'nullable|numeric', 'usage_limit' => 'nullable|integer|min:1', 'expires_at' => 'nullable|date']));
        return response()->json(['data' => $coupon->fresh()]);
    }

    public function destroy(Request $request, Coupon $coupon): JsonResponse
    {
        $this->authorize('delete', $coupon);
        $coupon->delete();
        return response()->json(['message' => 'Coupon deleted.']);
    }

    public function toggle(Request $request, Coupon $coupon): JsonResponse
    {
        $this->authorize('update', $coupon);
        $coupon->update(['expires_at' => $coupon->expires_at ? null : now()->subDay()]);
        return response()->json(['message' => 'Coupon toggled.', 'active' => ! $coupon->fresh()->expires_at?->isPast()]);
    }
}

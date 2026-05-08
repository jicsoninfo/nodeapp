<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(): JsonResponse { return response()->json(['data' => Coupon::with('vendor')->latest()->paginate(25)]); }
    public function store(Request $request): JsonResponse { return response()->json(['data' => Coupon::create($request->validate(['code'=>'required|unique:coupons,code','type'=>'required|in:percent,fixed,free_shipping','value'=>'required|numeric|min:0','min_order'=>'nullable|numeric','usage_limit'=>'nullable|integer','expires_at'=>'nullable|date']))], 201); }
    public function show(Coupon $coupon): JsonResponse { return response()->json(['data' => $coupon]); }
    public function update(Request $request, Coupon $coupon): JsonResponse { $coupon->update($request->all()); return response()->json(['data' => $coupon->fresh()]); }
    public function destroy(Coupon $coupon): JsonResponse { $coupon->delete(); return response()->json(['message' => 'Deleted.']); }
}

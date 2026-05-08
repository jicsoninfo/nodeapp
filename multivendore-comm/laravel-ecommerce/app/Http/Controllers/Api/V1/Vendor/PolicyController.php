<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Models\VendorPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->vendor->policies()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['type' => 'required|in:return,shipping,warranty', 'return_window_days' => 'nullable|integer|min:0', 'condition' => 'required|string|max:2000', 'lang_code' => 'required|string|exists:languages,code']);
        $policy = $request->user()->vendor->policies()->create($data);
        return response()->json(['data' => $policy], 201);
    }

    public function update(Request $request, VendorPolicy $policy): JsonResponse
    {
        abort_if($policy->vendor_id !== $request->user()->vendor->id, 403);
        $policy->update($request->validate(['return_window_days' => 'nullable|integer|min:0', 'condition' => 'sometimes|string|max:2000']));
        return response()->json(['data' => $policy->fresh()]);
    }

    public function destroy(Request $request, VendorPolicy $policy): JsonResponse
    {
        abort_if($policy->vendor_id !== $request->user()->vendor->id, 403);
        $policy->delete();
        return response()->json(['message' => 'Policy deleted.']);
    }
}

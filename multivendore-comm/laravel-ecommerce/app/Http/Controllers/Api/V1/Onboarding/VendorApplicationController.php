<?php
namespace App\Http\Controllers\Api\V1\Onboarding;
use App\Events\Vendor\VendorApplicationReceived;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\VendorResource;
use App\Models\Vendor;
use App\Models\VendorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorApplicationController extends Controller
{
    public function apply(Request $request): JsonResponse
    {
        $this->authorize('create', Vendor::class);

        $data = $request->validate([
            'store_name'          => 'required|string|max:200|unique:vendors,store_name',
            'business_type'       => 'required|in:individual,company,brand',
            'description'         => 'required|string|max:2000',
            'tax_id'              => 'nullable|string|max:100',
            'website_url'         => 'nullable|url|max:500',
            'plan_type'           => 'required|in:free,basic,pro,enterprise',
            'agree_terms'         => 'required|accepted',
        ]);

        $vendor = DB::transaction(function () use ($request, $data) {
            $vendor = Vendor::create([
                'owner_user_id' => $request->user()->id,
                'store_name'    => $data['store_name'],
                'plan_type'     => $data['plan_type'],
                'status'        => 'pending',
            ]);

            VendorProfile::create([
                'vendor_id'     => $vendor->id,
                'description'   => $data['description'],
                'business_type' => $data['business_type'],
                'tax_id'        => $data['tax_id'] ?? null,
                'website_url'   => $data['website_url'] ?? null,
            ]);

            $request->user()->assignRole('vendor');

            return $vendor;
        });

        event(new VendorApplicationReceived($vendor));

        return response()->json([
            'data'    => new VendorResource($vendor->load('profile')),
            'message' => 'Application submitted. Our team will review it within 2–3 business days.',
        ], 201);
    }

    public function status(Request $request): JsonResponse
    {
        $vendor = Vendor::where('owner_user_id', $request->user()->id)->with('profile')->first();

        if (! $vendor) {
            return response()->json(['data' => null, 'message' => 'No vendor application found.']);
        }

        return response()->json([
            'data' => [
                'store_name'  => $vendor->store_name,
                'status'      => $vendor->status,
                'applied_at'  => $vendor->created_at->toIso8601String(),
                'approved_at' => $vendor->approved_at?->toIso8601String(),
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Events\Vendor\VendorApproved;
use App\Events\Vendor\VendorSuspended;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct(private readonly VendorRepositoryInterface $vendors) {}

    public function index(Request $request): JsonResponse
    {
        $vendors = $this->vendors->paginate($request->all());
        return response()->json(['data' => VendorResource::collection($vendors)]);
    }

    public function approve(Vendor $vendor): JsonResponse
    {
        $vendor = $this->vendors->approve($vendor);
        event(new VendorApproved($vendor));

        return response()->json([
            'data'    => new VendorResource($vendor),
            'message' => 'Vendor approved successfully.',
        ]);
    }

    public function suspend(Request $request, Vendor $vendor): JsonResponse
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $vendor = $this->vendors->suspend($vendor, $request->reason);
        event(new VendorSuspended($vendor, $request->reason));

        return response()->json([
            'data'    => new VendorResource($vendor),
            'message' => 'Vendor suspended.',
        ]);
    }

    public function show(Vendor $vendor): JsonResponse
    {
        return response()->json([
            'data' => new VendorResource($vendor->load(['profile', 'owner', 'products'])),
        ]);
    }
}

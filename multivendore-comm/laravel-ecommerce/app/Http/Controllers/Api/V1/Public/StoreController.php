<?php
namespace App\Http\Controllers\Api\V1\Public;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\VendorResource;
use App\Models\Review;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vendors = Vendor::active()->with('profile')
            ->when($request->search, fn ($q) => $q->where('store_name','like',"%{$request->search}%"))
            ->paginate(20);
        return response()->json(['data' => VendorResource::collection($vendors)]);
    }

    public function show(string $slug): JsonResponse
    {
        $vendor = Vendor::where('slug', $slug)->active()->with(['profile','translations'])->firstOrFail();
        return response()->json(['data' => new VendorResource($vendor)]);
    }

    public function products(string $slug): JsonResponse
    {
        $vendor   = Vendor::where('slug', $slug)->active()->firstOrFail();
        $products = \App\Models\Product::where('vendor_id', $vendor->id)
            ->active()->with(['translations','variants','media','brand'])
            ->paginate(20);
        return response()->json(['data' => ProductResource::collection($products)]);
    }

    public function reviews(string $slug): JsonResponse
    {
        $vendor  = Vendor::where('slug', $slug)->active()->firstOrFail();
        $reviews = Review::whereHas('product', fn ($q) => $q->where('vendor_id', $vendor->id))
            ->approved()->with('user.profile')->latest()->paginate(20);
        return response()->json(['data' => $reviews]);
    }
}

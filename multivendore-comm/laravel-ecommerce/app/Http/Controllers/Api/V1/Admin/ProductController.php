<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['translations','vendor.profile','category','brand'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->vendor_id, fn ($q) => $q->where('vendor_id', $request->vendor_id))
            ->latest()->paginate(25);
        return response()->json(['data' => ProductResource::collection($products)]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => new ProductResource($product->load(['translations','variants','media','brand','category','vendor']))]);
    }

    public function updateStatus(Request $request, Product $product): JsonResponse
    {
        $request->validate(['status' => 'required|in:draft,active,inactive,archived']);
        $product->update(['status' => $request->status]);
        return response()->json(['message' => "Product status set to {$request->status}."]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted.']);
    }
}

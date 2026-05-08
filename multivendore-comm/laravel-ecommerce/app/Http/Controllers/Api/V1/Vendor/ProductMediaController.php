<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductMediaController extends Controller
{
    private function assertOwner(Request $request, Product $product): void
    {
        abort_if($product->vendor_id !== $request->user()->vendor->id, 403);
    }

    public function index(Request $request, Product $product): JsonResponse
    {
        $this->assertOwner($request, $product);
        return response()->json(['data' => $product->media()->orderBy('sort_order')->get()]);
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        $this->assertOwner($request, $product);
        $request->validate(['file' => 'required|file|mimes:jpg,jpeg,png,webp,mp4|max:51200', 'variant_id' => 'nullable|uuid', 'alt_text' => 'nullable|string|max:255']);
        $type = str_contains($request->file('file')->getMimeType(), 'video') ? 'video' : 'image';
        $path = $request->file('file')->store("products/{$product->id}", 's3');
        $count = $product->media()->count();
        $media = ProductMedia::create(['product_id' => $product->id, 'variant_id' => $request->variant_id, 'url' => $path, 'type' => $type, 'alt_text' => $request->alt_text, 'sort_order' => $count]);
        return response()->json(['data' => $media], 201);
    }

    public function destroy(Request $request, Product $product, ProductMedia $media): JsonResponse
    {
        $this->assertOwner($request, $product);
        abort_if($media->product_id !== $product->id, 404);
        $media->delete();
        return response()->json(['message' => 'Media deleted.']);
    }

    public function reorder(Request $request, Product $product): JsonResponse
    {
        $this->assertOwner($request, $product);
        $request->validate(['order' => 'required|array', 'order.*' => 'uuid']);
        foreach ($request->order as $i => $id) {
            ProductMedia::where('id', $id)->where('product_id', $product->id)->update(['sort_order' => $i]);
        }
        return response()->json(['message' => 'Media reordered.']);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Jobs\RecordProductView;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductRepositoryInterface $products) {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->products->paginate($request->all());
        return response()->json(['data' => ProductResource::collection($products)]);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        abort_unless($product->status->isVisible(), 404);

        $product->load(['translations', 'variants.attributeValues', 'media', 'brand', 'category', 'vendor.profile', 'approvedReviews.user']);

        // Async view tracking
        RecordProductView::dispatch(
            $product->id,
            $request->user()?->id,
            $request->header('X-Session-Id'),
            $request->header('X-Referrer-Type')
        );

        return response()->json(['data' => new ProductResource($product)]);
    }

    public function featured(): JsonResponse
    {
        $products = $this->products->getFeatured(10);
        return response()->json(['data' => ProductResource::collection($products)]);
    }
}

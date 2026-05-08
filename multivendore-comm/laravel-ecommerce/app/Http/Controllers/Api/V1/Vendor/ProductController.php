<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly VendorRepositoryInterface  $vendors,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $vendor   = $this->vendors->findByOwner($request->user()->id);
        abort_unless($vendor?->canSell(), 403, 'Your store is not active.');

        $products = $this->products->getByVendor($vendor->id, $request->all());

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $vendor = $this->vendors->findByOwner($request->user()->id);
        abort_unless($vendor?->canSell(), 403, 'Your store is not active.');

        $data = $request->validated();
        $data['product']['vendor_id'] = $vendor->id;

        $product = $this->products->create($data);

        return response()->json([
            'data'    => new ProductResource($product),
            'message' => 'Product created successfully.',
        ], 201);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        $vendor = $this->vendors->findByOwner($request->user()->id);
        abort_if($product->vendor_id !== $vendor?->id, 403);

        return response()->json([
            'data' => new ProductResource($product->load(['translations', 'variants', 'media'])),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $vendor = $this->vendors->findByOwner($request->user()->id);
        abort_if($product->vendor_id !== $vendor?->id, 403);

        $product = $this->products->update($product, $request->validated());

        return response()->json([
            'data'    => new ProductResource($product),
            'message' => 'Product updated successfully.',
        ]);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $vendor = $this->vendors->findByOwner($request->user()->id);
        abort_if($product->vendor_id !== $vendor?->id, 403);

        $this->products->delete($product);

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}

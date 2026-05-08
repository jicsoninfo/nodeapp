<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\CreateVariantRequest;
use App\Http\Resources\V1\VariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    private function assertOwner(Request $request, Product $product): void
    {
        abort_if($product->vendor_id !== $request->user()->vendor->id, 403, 'Not your product.');
    }

    public function index(Request $request, Product $product): JsonResponse
    {
        $this->assertOwner($request, $product);
        return response()->json(['data' => VariantResource::collection($product->variants()->with('attributeValues')->get())]);
    }

    public function store(CreateVariantRequest $request, Product $product): JsonResponse
    {
        $this->assertOwner($request, $product);
        $variant = $product->variants()->create($request->validated());
        if ($request->attribute_values) {
            foreach ($request->attribute_values as $avId) {
                $av = \App\Models\AttributeValue::find($avId);
                if ($av) $variant->attributeValues()->attach($avId, ['attribute_id' => $av->attribute_id]);
            }
        }
        return response()->json(['data' => new VariantResource($variant->load('attributeValues'))], 201);
    }

    public function update(Request $request, Product $product, ProductVariant $variant): JsonResponse
    {
        $this->assertOwner($request, $product);
        $variant->update($request->validate(['price' => 'sometimes|numeric|min:0', 'sale_price' => 'nullable|numeric', 'stock_quantity' => 'sometimes|integer|min:0', 'is_active' => 'boolean']));
        return response()->json(['data' => new VariantResource($variant->fresh())]);
    }

    public function destroy(Request $request, Product $product, ProductVariant $variant): JsonResponse
    {
        $this->assertOwner($request, $product);
        $variant->delete();
        return response()->json(['message' => 'Variant deleted.']);
    }
}

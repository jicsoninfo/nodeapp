<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vendorId = $request->user()->vendor->id;
        $variants = ProductVariant::whereHas('product', fn ($q) => $q->where('vendor_id', $vendorId))
            ->with('product.translations')->paginate(50);
        return response()->json(['data' => $variants]);
    }

    public function updateStock(Request $request, ProductVariant $variant): JsonResponse
    {
        abort_if($variant->product->vendor_id !== $request->user()->vendor->id, 403);
        $request->validate(['stock_quantity' => 'required|integer|min:0']);
        $variant->update(['stock_quantity' => $request->stock_quantity]);
        return response()->json(['data' => $variant->fresh(), 'message' => 'Stock updated.']);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate(['items' => 'required|array', 'items.*.sku' => 'required|string', 'items.*.stock_quantity' => 'required|integer|min:0']);
        $vendorId = $request->user()->vendor->id;
        $updated  = 0;

        foreach ($request->items as $item) {
            $variant = ProductVariant::where('sku', $item['sku'])->whereHas('product', fn ($q) => $q->where('vendor_id', $vendorId))->first();
            if ($variant) { $variant->update(['stock_quantity' => $item['stock_quantity']]); $updated++; }
        }

        return response()->json(['message' => "{$updated} variants updated."]);
    }

    public function lowStock(Request $request): JsonResponse
    {
        $threshold = $request->get('threshold', 5);
        $vendorId  = $request->user()->vendor->id;
        $variants  = ProductVariant::whereHas('product', fn ($q) => $q->where('vendor_id', $vendorId))
            ->where('stock_quantity', '<=', $threshold)->where('is_active', true)
            ->with('product.translations')->get();
        return response()->json(['data' => $variants]);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $vendorId = $request->user()->vendor->id;
        $variants = ProductVariant::whereHas('product', fn ($q) => $q->where('vendor_id', $vendorId))->with('product.translations')->get();
        return response()->streamDownload(function () use ($variants) {
            echo "SKU,Product,Price,Stock,Active\n";
            foreach ($variants as $v) {
                $name = $v->product->translations->firstWhere('lang_code', 'en')?->name ?? '';
                echo "{$v->sku},{$name},{$v->price},{$v->stock_quantity}," . ($v->is_active ? 'yes' : 'no') . "\n";
            }
        }, 'inventory.csv', ['Content-Type' => 'text/csv']);
    }
}

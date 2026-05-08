<?php
namespace App\Http\Controllers\Api\V1\Buyer;
use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => Wishlist::where('user_id', $request->user()->id)->with('items')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => 'required|string|max:200', 'is_public' => 'boolean']);
        $wishlist = Wishlist::create(array_merge($data, ['user_id' => $request->user()->id]));
        return response()->json(['data' => $wishlist], 201);
    }

    public function show(Request $request, Wishlist $wishlist): JsonResponse
    {
        abort_if($wishlist->user_id !== $request->user()->id && ! $wishlist->is_public, 403);
        return response()->json(['data' => $wishlist->load('items.variant')]);
    }

    public function update(Request $request, Wishlist $wishlist): JsonResponse
    {
        abort_if($wishlist->user_id !== $request->user()->id, 403);
        $wishlist->update($request->validate(['name' => 'sometimes|string|max:200', 'is_public' => 'boolean']));
        return response()->json(['data' => $wishlist->fresh()]);
    }

    public function destroy(Request $request, Wishlist $wishlist): JsonResponse
    {
        abort_if($wishlist->user_id !== $request->user()->id, 403);
        $wishlist->delete();
        return response()->json(['message' => 'Wishlist deleted.']);
    }

    public function addItem(Request $request, Wishlist $wishlist): JsonResponse
    {
        abort_if($wishlist->user_id !== $request->user()->id, 403);
        $request->validate(['variant_id' => 'required|uuid|exists:product_variants,id']);
        $item = WishlistItem::firstOrCreate(['wishlist_id' => $wishlist->id, 'variant_id' => $request->variant_id], ['added_at' => now()]);
        return response()->json(['data' => $item, 'message' => 'Added to wishlist.']);
    }

    public function removeItem(Request $request, Wishlist $wishlist, WishlistItem $item): JsonResponse
    {
        abort_if($wishlist->user_id !== $request->user()->id, 403);
        $item->delete();
        return response()->json(['message' => 'Removed from wishlist.']);
    }

    public function moveToCart(Request $request, Wishlist $wishlist): JsonResponse
    {
        abort_if($wishlist->user_id !== $request->user()->id, 403);
        $cartService = app(\App\Contracts\Services\CartServiceInterface::class);
        $cart        = $cartService->getOrCreate($request->user());
        foreach ($wishlist->items as $item) {
            try { $cartService->addItem($cart, $item->variant_id, 1); } catch (\Throwable) {}
        }
        return response()->json(['message' => 'Wishlist items moved to cart.']);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Contracts\Services\CartServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CartResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartServiceInterface $cartService) {}

    public function show(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreate($request->user());
        return response()->json(['data' => new CartResource($cart->load('items.variant'))]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'variant_id' => 'required|uuid|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1|max:100',
        ]);

        $cart = $this->cartService->getOrCreate($request->user());
        $cart = $this->cartService->addItem($cart, $request->variant_id, $request->quantity);

        return response()->json(['data' => new CartResource($cart->load('items.variant'))]);
    }

    public function removeItem(Request $request, string $cartItemId): JsonResponse
    {
        $cart = $this->cartService->getOrCreate($request->user());
        $cart = $this->cartService->removeItem($cart, $cartItemId);

        return response()->json(['data' => new CartResource($cart->load('items.variant'))]);
    }

    public function updateItem(Request $request, string $cartItemId): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:100']);

        $cart = $this->cartService->getOrCreate($request->user());
        $cart = $this->cartService->updateQuantity($cart, $cartItemId, $request->quantity);

        return response()->json(['data' => new CartResource($cart->load('items.variant'))]);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:50']);

        $cart = $this->cartService->getOrCreate($request->user());
        $cart = $this->cartService->applyCoupon($cart, $request->code);

        return response()->json([
            'data'    => new CartResource($cart->load('items.variant')),
            'message' => 'Coupon applied successfully.',
        ]);
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreate($request->user());
        $cart = $this->cartService->removeCoupon($cart);

        return response()->json(['data' => new CartResource($cart->load('items.variant'))]);
    }
}

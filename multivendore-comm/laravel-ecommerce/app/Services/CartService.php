<?php

namespace App\Services;

use App\Contracts\Services\CartServiceInterface;
use App\Exceptions\InvalidCouponException;
use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService implements CartServiceInterface
{
    public function getOrCreate(User|string|null $userOrSession): Cart
    {
        if ($userOrSession instanceof User) {
            return Cart::firstOrCreate(
                ['user_id' => $userOrSession->id],
                ['currency' => 'USD', 'expires_at' => now()->addDays(7)]
            );
        }

        return Cart::firstOrCreate(
            ['session_id' => $userOrSession],
            ['currency' => 'USD', 'expires_at' => now()->addHours(24)]
        );
    }

    public function addItem(Cart $cart, string $variantId, int $quantity): Cart
    {
        $variant = ProductVariant::findOrFail($variantId);

        throw_if(
            $variant->stock_quantity < $quantity || ! $variant->is_active,
            InsufficientStockException::class,
            "Item is out of stock or unavailable."
        );

        $existing = $cart->items()->where('variant_id', $variantId)->first();

        if ($existing) {
            $newQty = $existing->quantity + $quantity;
            throw_if(
                $variant->stock_quantity < $newQty,
                InsufficientStockException::class,
                "Cannot add {$quantity} more — only {$variant->stock_quantity} in stock."
            );
            $existing->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'variant_id' => $variantId,
                'vendor_id'  => $variant->product->vendor_id,
                'quantity'   => $quantity,
                'unit_price' => $variant->effective_price,
            ]);
        }

        return $cart->load('items');
    }

    public function removeItem(Cart $cart, string $cartItemId): Cart
    {
        $cart->items()->where('id', $cartItemId)->delete();
        return $cart->load('items');
    }

    public function updateQuantity(Cart $cart, string $cartItemId, int $quantity): Cart
    {
        $item    = $cart->items()->findOrFail($cartItemId);
        $variant = ProductVariant::findOrFail($item->variant_id);

        throw_if(
            $variant->stock_quantity < $quantity,
            InsufficientStockException::class,
            "Only {$variant->stock_quantity} units available."
        );

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        return $cart->load('items');
    }

    public function applyCoupon(Cart $cart, string $code): Cart
    {
        $coupon = Coupon::where('code', strtoupper($code))->firstOrFail();

        throw_unless(
            $coupon->isValid($cart->total),
            InvalidCouponException::class,
            "Coupon '{$code}' is not valid for this cart."
        );

        $cart->update(['coupon_id' => $coupon->id]);

        return $cart->load('items');
    }

    public function removeCoupon(Cart $cart): Cart
    {
        $cart->update(['coupon_id' => null]);
        return $cart->load('items');
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function merge(Cart $guestCart, Cart $userCart): Cart
    {
        DB::transaction(function () use ($guestCart, $userCart) {
            foreach ($guestCart->items as $guestItem) {
                $existing = $userCart->items()
                    ->where('variant_id', $guestItem->variant_id)
                    ->first();

                if ($existing) {
                    $existing->increment('quantity', $guestItem->quantity);
                } else {
                    $userCart->items()->create([
                        'variant_id' => $guestItem->variant_id,
                        'vendor_id'  => $guestItem->vendor_id,
                        'quantity'   => $guestItem->quantity,
                        'unit_price' => $guestItem->unit_price,
                    ]);
                }
            }

            $guestCart->items()->delete();
            $guestCart->delete();
        });

        return $userCart->load('items');
    }
}

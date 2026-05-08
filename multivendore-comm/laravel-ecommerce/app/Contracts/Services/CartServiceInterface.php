<?php

namespace App\Contracts\Services;

use App\Models\Cart;
use App\Models\User;

interface CartServiceInterface
{
    public function getOrCreate(User|string|null $userOrSession): Cart;
    public function addItem(Cart $cart, string $variantId, int $quantity): Cart;
    public function removeItem(Cart $cart, string $cartItemId): Cart;
    public function updateQuantity(Cart $cart, string $cartItemId, int $quantity): Cart;
    public function applyCoupon(Cart $cart, string $code): Cart;
    public function removeCoupon(Cart $cart): Cart;
    public function clear(Cart $cart): void;
    public function merge(Cart $guestCart, Cart $userCart): Cart;
}

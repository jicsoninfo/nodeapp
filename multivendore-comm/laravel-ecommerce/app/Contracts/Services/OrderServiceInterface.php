<?php

namespace App\Contracts\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;

interface OrderServiceInterface
{
    public function placeOrder(User $user, Cart $cart, array $data): Order;
    public function cancelOrder(Order $order, string $reason = ''): Order;
    public function processRefund(Order $order, float $amount): void;
}

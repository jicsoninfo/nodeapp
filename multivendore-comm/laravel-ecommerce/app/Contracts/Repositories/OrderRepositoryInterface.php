<?php

namespace App\Contracts\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function findById(string $id): ?Order;
    public function findByOrderNumber(string $orderNumber): ?Order;
    public function findByUser(string $userId, array $filters = []): LengthAwarePaginator;
    public function findByVendor(string $vendorId, array $filters = []): LengthAwarePaginator;
    public function create(array $data): Order;
    public function updateStatus(Order $order, string $status): Order;
    public function getRevenueByVendor(string $vendorId, string $from, string $to): float;
}

<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->where('order_number', $orderNumber)
            ->with(['items.variant', 'items.vendor', 'payment', 'user', 'address'])
            ->first();
    }

    public function findByUser(string $userId, array $filters = []): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['items', 'payment'])
            ->where('user_id', $userId)
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest('placed_at')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findByVendor(string $vendorId, array $filters = []): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['items' => fn ($q) => $q->where('vendor_id', $vendorId), 'user', 'address'])
            ->whereHas('items', fn ($q) => $q->where('vendor_id', $vendorId))
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest('placed_at')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = $this->model->create($data['order']);
            foreach ($data['items'] as $item) {
                $order->items()->create($item);
            }
            return $order->load(['items', 'user', 'address']);
        });
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order->refresh();
    }

    public function getRevenueByVendor(string $vendorId, string $from, string $to): float
    {
        return (float) DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.vendor_id', $vendorId)
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.placed_at', [$from, $to])
            ->sum(DB::raw('order_items.unit_price * order_items.quantity'));
    }
}

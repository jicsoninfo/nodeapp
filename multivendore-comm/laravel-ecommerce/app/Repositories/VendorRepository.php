<?php

namespace App\Repositories;

use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Enums\VendorStatus;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VendorRepository extends BaseRepository implements VendorRepositoryInterface
{
    public function __construct(Vendor $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?Vendor
    {
        return $this->model->with(['profile', 'translations'])->where('slug', $slug)->first();
    }

    public function findByOwner(string $userId): ?Vendor
    {
        return $this->model->where('owner_user_id', $userId)->with(['profile'])->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['profile', 'owner'])
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['plan_type']), fn ($q) => $q->where('plan_type', $filters['plan_type']))
            ->latest()
            ->paginate($perPage);
    }

    public function approve(Vendor $vendor): Vendor
    {
        $vendor->update([
            'status'      => VendorStatus::Active,
            'approved_at' => now(),
        ]);
        return $vendor->refresh();
    }

    public function suspend(Vendor $vendor, string $reason): Vendor
    {
        $vendor->update(['status' => VendorStatus::Suspended]);
        activity()->on($vendor)->log("Suspended: {$reason}");
        return $vendor->refresh();
    }
}

<?php

namespace App\Contracts\Repositories;

use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VendorRepositoryInterface
{
    public function findById(string $id): ?Vendor;
    public function findBySlug(string $slug): ?Vendor;
    public function findByOwner(string $userId): ?Vendor;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Vendor;
    public function update(Vendor $vendor, array $data): Vendor;
    public function approve(Vendor $vendor): Vendor;
    public function suspend(Vendor $vendor, string $reason): Vendor;
}

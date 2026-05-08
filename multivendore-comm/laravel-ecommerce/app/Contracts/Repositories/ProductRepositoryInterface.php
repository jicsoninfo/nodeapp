<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function findById(string $id): ?Product;
    public function findByAsin(string $asin): ?Product;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): bool;
    public function getByVendor(string $vendorId, array $filters = []): LengthAwarePaginator;
    public function getFeatured(int $limit = 10): Collection;
    public function updateRating(string $productId): void;
}

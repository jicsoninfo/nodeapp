<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findByAsin(string $asin): ?Product
    {
        return $this->model->where('asin', $asin)->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['translations', 'variants', 'media', 'brand', 'category', 'vendor.profile'])
            ->active();

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['min_price'])) {
            $query->whereHas('variants', fn ($q) =>
                $q->where('price', '>=', $filters['min_price'])
            );
        }

        if (isset($filters['max_price'])) {
            $query->whereHas('variants', fn ($q) =>
                $q->where('price', '<=', $filters['max_price'])
            );
        }

        if (isset($filters['sort'])) {
            match ($filters['sort']) {
                'price_asc'  => $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) ASC'),
                'price_desc' => $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) DESC'),
                'rating'     => $query->orderByDesc('avg_rating'),
                'newest'     => $query->latest(),
                default      => $query->latest(),
            };
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = $this->model->create($data['product']);

            foreach ($data['translations'] as $translation) {
                $product->translations()->create($translation);
            }

            foreach ($data['variants'] as $variant) {
                $v = $product->variants()->create($variant['data']);
                if (! empty($variant['attribute_values'])) {
                    foreach ($variant['attribute_values'] as $avId) {
                        $v->attributeValues()->attach($avId, ['attribute_id' => $variant['attribute_ids'][$avId] ?? null]);
                    }
                }
            }

            return $product->load(['translations', 'variants', 'media']);
        });
    }

    public function getByVendor(string $vendorId, array $filters = []): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['translations', 'variants', 'category'])
            ->where('vendor_id', $vendorId)
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest()
            ->paginate($filters['per_page'] ?? 20);
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return $this->model->newQuery()
            ->with(['translations', 'variants', 'media'])
            ->active()
            ->orderByDesc('avg_rating')
            ->limit($limit)
            ->get();
    }

    public function updateRating(string $productId): void
    {
        $stats = DB::table('reviews')
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->selectRaw('AVG(rating) as avg, COUNT(*) as total')
            ->first();

        $this->model->where('id', $productId)->update([
            'avg_rating'    => round($stats->avg ?? 0, 2),
            'total_reviews' => $stats->total ?? 0,
        ]);
    }
}

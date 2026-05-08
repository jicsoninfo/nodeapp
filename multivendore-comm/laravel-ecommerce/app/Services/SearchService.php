<?php
namespace App\Services;

use App\Models\Product;
use App\Models\SearchQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class SearchService
{
    /**
     * Full-text product search via Laravel Scout (Algolia / Meilisearch / DB fallback).
     * Logs the query for analytics.
     */
    public function search(
        string  $query,
        array   $filters   = [],
        int     $perPage   = 20,
        ?string $userId    = null,
        ?string $sessionId = null,
        string  $locale    = 'en',
    ): LengthAwarePaginator {
        $builder = Product::search($query)->where('status', 'active');

        if (isset($filters['category_id'])) {
            $builder->where('category_id', $filters['category_id']);
        }

        if (isset($filters['brand_id'])) {
            $builder->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['vendor_id'])) {
            $builder->where('vendor_id', $filters['vendor_id']);
        }

        $results = $builder->paginate($perPage);

        // Log asynchronously to avoid slowing down the response
        try {
            SearchQuery::create([
                'user_id'       => $userId,
                'session_id'    => $sessionId,
                'query'         => $query,
                'lang_code'     => $locale,
                'results_count' => $results->total(),
                'searched_at'   => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log search query', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /** Get trending searches in the last N days. */
    public function trending(int $days = 7, int $limit = 10): array
    {
        return \Illuminate\Support\Facades\DB::table('search_queries')
            ->where('searched_at', '>=', now()->subDays($days))
            ->selectRaw('query, COUNT(*) as count')
            ->groupBy('query')
            ->orderByDesc('count')
            ->take($limit)
            ->pluck('count', 'query')
            ->toArray();
    }

    /** Autocomplete suggestions from past successful queries. */
    public function suggestions(string $prefix, int $limit = 5): array
    {
        return \Illuminate\Support\Facades\DB::table('search_queries')
            ->where('query', 'like', "{$prefix}%")
            ->where('results_count', '>', 0)
            ->selectRaw('query, COUNT(*) as count')
            ->groupBy('query')
            ->orderByDesc('count')
            ->take($limit)
            ->pluck('query')
            ->toArray();
    }
}

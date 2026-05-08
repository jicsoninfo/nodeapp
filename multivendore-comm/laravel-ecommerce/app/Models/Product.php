<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasUuid, HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'vendor_id', 'category_id', 'brand_id',
        'asin', 'status', 'avg_rating', 'total_reviews',
    ];

    protected $casts = [
        'status'        => ProductStatus::class,
        'avg_rating'    => 'decimal:2',
        'total_reviews' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    // ── Scout / Search ────────────────────────────────────
    public function toSearchableArray(): array
    {
        return [
            'id'          => $this->id,
            'asin'        => $this->asin,
            'vendor_id'   => $this->vendor_id,
            'category_id' => $this->category_id,
            'brand_id'    => $this->brand_id,
            'status'      => $this->status->value,
            'avg_rating'  => $this->avg_rating,
            'names'       => $this->translations->pluck('name')->toArray(),
        ];
    }

    // ── Scopes ────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', ProductStatus::Active);
    }

    public function scopeForVendor($query, string $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    // ── Helpers ───────────────────────────────────────────
    public function getTranslation(string $locale, string $fallback = 'en'): ?ProductTranslation
    {
        return $this->translations->firstWhere('lang_code', $locale)
            ?? $this->translations->firstWhere('lang_code', $fallback);
    }

    public function getLowestPrice(): ?float
    {
        return $this->activeVariants->min('price');
    }
}

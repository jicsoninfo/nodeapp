<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasTranslations;

    // ── Translation config ───────────────────────────────────────────────────
    protected string $translationModel = ProductTranslation::class;
    protected string $translationFk    = 'product_id';
    protected array  $translatable     = ['name', 'description', 'short_description', 'meta_title', 'meta_description'];

    protected $fillable = [
        'sku', 'slug', 'category_id', 'is_active',
        'sort_order', 'stock_quantity', 'main_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    // ── Price helpers ────────────────────────────────────────────────────────

    /**
     * Get the price for a given currency and price list.
     *
     * Usage:
     *   $product->price('USD')
     *   $product->price('EUR', 'wholesale')
     */
    public function price(string $currencyCode = null, string $priceListCode = 'retail'): ?ProductPrice
    {
        $currencyCode = $currencyCode ?? Currency::getDefaultCode();

        return $this->prices
            ->filter(fn (ProductPrice $p) =>
                $p->currency_code === $currencyCode
                && $p->priceList->code === $priceListCode
            )
            ->first();
    }

    /**
     * Formatted price string, e.g. "$29.99" or "29.99 €"
     */
    public function formattedPrice(string $currencyCode = null, string $priceListCode = 'retail'): string
    {
        $price = $this->price($currencyCode, $priceListCode);

        if (! $price) {
            return '—';
        }

        return $price->formatted();
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}

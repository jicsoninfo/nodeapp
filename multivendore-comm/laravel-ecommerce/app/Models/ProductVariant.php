<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasUuid;

    protected $fillable = [
        'product_id', 'sku', 'price', 'sale_price', 'currency',
        'stock_quantity', 'weight_grams', 'is_active',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'sale_price'     => 'decimal:2',
        'stock_quantity' => 'integer',
        'weight_grams'   => 'integer',
        'is_active'      => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attributes',
            'variant_id',
            'attribute_value_id'
        )->withPivot('attribute_id');
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function isInStock(): bool
    {
        return $this->is_active && $this->stock_quantity > 0;
    }

    public function decrementStock(int $qty = 1): bool
    {
        if ($this->stock_quantity < $qty) return false;
        $this->decrement('stock_quantity', $qty);
        return true;
    }

    public function incrementStock(int $qty = 1): void
    {
        $this->increment('stock_quantity', $qty);
    }
}

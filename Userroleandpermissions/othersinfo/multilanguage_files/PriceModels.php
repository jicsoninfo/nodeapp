<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// ── Currency ──────────────────────────────────────────────────────────────────

class Currency extends Model
{
    protected $fillable = [
        'code', 'name', 'symbol', 'symbol_position',
        'decimal_places', 'exchange_rate', 'is_active', 'is_default',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_default'     => 'boolean',
        'exchange_rate'  => 'float',
        'decimal_places' => 'integer',
    ];

    public static function getDefaultCode(): string
    {
        return static::where('is_default', true)->value('code') ?? 'USD';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Format an amount according to this currency's rules.
     */
    public function format(float $amount): string
    {
        $formatted = number_format($amount, $this->decimal_places);

        return $this->symbol_position === 'before'
            ? $this->symbol . $formatted
            : $formatted . ' ' . $this->symbol;
    }
}

// ── PriceList ─────────────────────────────────────────────────────────────────

class PriceList extends Model
{
    protected $fillable = ['name', 'code', 'is_default', 'is_active'];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public static function getDefault(): self
    {
        return static::where('is_default', true)->firstOrFail();
    }
}

// ── ProductPrice ──────────────────────────────────────────────────────────────

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id', 'price_list_id', 'currency_code',
        'amount', 'sale_amount', 'sale_starts_at', 'sale_ends_at',
    ];

    protected $casts = [
        'amount'         => 'float',
        'sale_amount'    => 'float',
        'sale_starts_at' => 'datetime',
        'sale_ends_at'   => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isOnSale(): bool
    {
        if (! $this->sale_amount) {
            return false;
        }

        $now = now();

        if ($this->sale_starts_at && $now->lt($this->sale_starts_at)) {
            return false;
        }

        if ($this->sale_ends_at && $now->gt($this->sale_ends_at)) {
            return false;
        }

        return true;
    }

    public function effectiveAmount(): float
    {
        return $this->isOnSale() ? $this->sale_amount : $this->amount;
    }

    public function formatted(): string
    {
        return $this->currency->format($this->effectiveAmount());
    }

    public function formattedOriginal(): string
    {
        return $this->currency->format($this->amount);
    }
}

// ── ProductImage ──────────────────────────────────────────────────────────────

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'path', 'alt_text', 'sort_order'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

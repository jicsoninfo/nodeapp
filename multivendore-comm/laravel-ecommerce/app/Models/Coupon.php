<?php

namespace App\Models;

use App\Enums\CouponType;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    use HasUuid;

    protected $fillable = [
        'code', 'type', 'value', 'min_order',
        'usage_limit', 'used_count', 'vendor_id', 'expires_at',
    ];

    protected $casts = [
        'type'       => CouponType::class,
        'value'      => 'decimal:2',
        'min_order'  => 'decimal:2',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function isValid(float $orderTotal): bool
    {
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($this->min_order && $orderTotal < $this->min_order) return false;
        return true;
    }

    public function computeDiscount(float $orderTotal): float
    {
        return $this->type->computeDiscount($orderTotal, $this->value);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}

<?php

namespace App\Models;

use App\Enums\FulfillmentStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_id', 'vendor_id', 'variant_id',
        'quantity', 'unit_price', 'tax_rate', 'fulfillment_status',
    ];

    protected $casts = [
        'unit_price'         => 'decimal:2',
        'tax_rate'           => 'decimal:2',
        'quantity'           => 'integer',
        'fulfillment_status' => FulfillmentStatus::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function getSubtotalAttribute(): float
    {
        return round($this->unit_price * $this->quantity, 2);
    }

    public function getTaxAmountAttribute(): float
    {
        return round($this->subtotal * ($this->tax_rate / 100), 2);
    }
}

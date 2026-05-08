<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasUuid;

    protected $fillable = ['cart_id', 'variant_id', 'vendor_id', 'quantity', 'unit_price'];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

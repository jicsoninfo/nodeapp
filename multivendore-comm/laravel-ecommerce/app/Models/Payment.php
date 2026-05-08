<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_id', 'method', 'provider', 'provider_txn_id',
        'amount', 'currency', 'status', 'processed_at', 'meta',
    ];

    protected $casts = [
        'method'       => PaymentMethod::class,
        'status'       => PaymentStatus::class,
        'amount'       => 'decimal:2',
        'processed_at' => 'datetime',
        'meta'         => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

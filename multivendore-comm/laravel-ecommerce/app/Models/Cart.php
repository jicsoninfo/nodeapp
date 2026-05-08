<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasUuid;

    protected $fillable = ['user_id', 'session_id', 'currency', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum(fn ($item) => $item->unit_price * $item->quantity);
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}

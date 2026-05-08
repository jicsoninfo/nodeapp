<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use HasUuid, HasFactory, LogsActivity;

    protected $fillable = [
        'user_id', 'address_id', 'coupon_id', 'order_number',
        'status', 'currency', 'subtotal', 'tax_amount',
        'shipping_amount', 'discount_amount', 'total_amount',
        'notes', 'placed_at',
    ];

    protected $casts = [
        'status'          => OrderStatus::class,
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'placed_at'       => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // ── State machine ─────────────────────────────────────
    public function canTransitionTo(OrderStatus $next): bool
    {
        return in_array($next, $this->status->transitions());
    }

    public function transitionTo(OrderStatus $next): void
    {
        if (! $this->canTransitionTo($next)) {
            throw new \LogicException("Cannot transition from {$this->status->value} to {$next->value}");
        }
        $this->update(['status' => $next]);
    }

    // ── Scopes ────────────────────────────────────────────
    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status);
    }

    // ── Helpers ───────────────────────────────────────────
    public static function generateOrderNumber(): string
    {
        return 'ORD-' . date('Y') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->useLogName('order');
    }
}

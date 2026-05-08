<?php

namespace App\Models;

use App\Enums\VendorStatus;
use App\Enums\PlanType;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Vendor extends Model
{
    use HasUuid, HasSlug, SoftDeletes, LogsActivity;

    protected $fillable = [
        'owner_user_id', 'store_name', 'slug', 'status',
        'plan_type', 'commission_rate', 'approved_at',
    ];

    protected $casts = [
        'status'          => VendorStatus::class,
        'plan_type'       => PlanType::class,
        'commission_rate' => 'decimal:2',
        'approved_at'     => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('store_name')
            ->saveSlugsTo('slug');
    }

    // ── Relationships ────────────────────────────────────
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(VendorProfile::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(VendorBankAccount::class);
    }

    public function primaryBankAccount(): HasOne
    {
        return $this->hasOne(VendorBankAccount::class)->where('is_primary', true);
    }

    public function policies(): HasMany
    {
        return $this->hasMany(VendorPolicy::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(VendorTranslation::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(VendorPayout::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', VendorStatus::Active);
    }

    public function scopePending($query)
    {
        return $query->where('status', VendorStatus::Pending);
    }

    // ── Helpers ───────────────────────────────────────────
    public function canSell(): bool
    {
        return $this->status->canSell();
    }

    public function effectiveCommissionRate(): float
    {
        return $this->commission_rate - $this->plan_type->commissionDiscount();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'plan_type', 'commission_rate'])
            ->logOnlyDirty()
            ->useLogName('vendor');
    }
}

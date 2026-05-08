<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasUuid, SoftDeletes, LogsActivity;

    protected $fillable = [
        'email', 'phone', 'password', 'status',
        'email_verified_at', 'phone_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'status'              => UserStatus::class,
        'email_verified_at'   => 'datetime',
        'phone_verified_at'   => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class, 'owner_user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function authTokens(): HasMany
    {
        return $this->hasMany(AuthToken::class);
    }

    // ── Helpers ──────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isVendor(): bool
    {
        return $this->hasRole('vendor');
    }

    public function canLogin(): bool
    {
        return $this->status->canLogin();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->profile?->first_name} {$this->profile?->last_name}");
    }

    // ── Activity Log ─────────────────────────────────────
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['email', 'status'])
            ->logOnlyDirty()
            ->useLogName('user');
    }
}

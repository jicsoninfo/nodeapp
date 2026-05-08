<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CouponPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) return true;
        return null;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('vendor');
    }

    public function update(User $user, Coupon $coupon): Response|bool
    {
        if (! $coupon->vendor_id) {
            return Response::deny('Platform-wide coupons are managed by admins only.');
        }

        return $user->vendor?->id === $coupon->vendor_id
            ? true
            : Response::deny('You can only edit your own coupons.');
    }

    public function delete(User $user, Coupon $coupon): Response|bool
    {
        if (! $coupon->vendor_id) {
            return Response::deny('Platform-wide coupons are managed by admins only.');
        }

        return $user->vendor?->id === $coupon->vendor_id
            ? true
            : Response::deny('You can only delete your own coupons.');
    }
}

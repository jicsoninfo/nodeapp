<?php

namespace App\Policies;

use App\Enums\VendorStatus;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * Authorization rules for Vendor model actions.
 *
 * Actors:
 *  1. Vendor owner — manages their own store
 *  2. Admin        — full control via before()
 *  3. Support      — read-only + limited actions
 */
class VendorPolicy
{
    use HandlesAuthorization;

    /**
     * Admins bypass all checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Anyone can view an active vendor's public store.
     * Support can view any vendor regardless of status.
     */
    public function view(?User $user, Vendor $vendor): Response|bool
    {
        if ($vendor->status === VendorStatus::Active) {
            return true;
        }

        if (! $user) {
            return Response::deny('This store is not currently available.');
        }

        if ($user->hasRole('support')) {
            return true;
        }

        // Owner can always see their own store
        if ($vendor->owner_user_id === $user->id) {
            return true;
        }

        return Response::deny('This store is not currently available.');
    }

    /**
     * Each user can only apply to create one vendor store.
     * Must have a verified email.
     */
    public function create(User $user): Response|bool
    {
        if (! $user->hasVerifiedEmail()) {
            return Response::deny('You must verify your email address before applying as a vendor.');
        }

        $existingVendor = Vendor::where('owner_user_id', $user->id)->first();

        if ($existingVendor) {
            return match ($existingVendor->status) {
                VendorStatus::Pending   => Response::deny('Your vendor application is already under review.'),
                VendorStatus::Active    => Response::deny('You already have an active store.'),
                VendorStatus::Suspended => Response::deny('Your store is suspended. Contact support.'),
                VendorStatus::Rejected  => true, // Allow re-application after rejection
            };
        }

        return true;
    }

    /**
     * Only the store owner can update their store profile.
     */
    public function update(User $user, Vendor $vendor): Response|bool
    {
        if (! $this->isOwner($user, $vendor)) {
            return Response::deny('You can only update your own store.');
        }

        if ($vendor->status === VendorStatus::Suspended) {
            return Response::deny('You cannot update a suspended store. Contact support.');
        }

        return true;
    }

    /**
     * Platform admins can approve vendors (handled by before()).
     * This gate is never reached by non-admins.
     */
    public function approve(User $user, Vendor $vendor): Response|bool
    {
        if ($vendor->status !== VendorStatus::Pending) {
            return Response::deny("Vendor is already {$vendor->status->value}.");
        }

        return Response::deny('Only admins can approve vendors.');
    }

    /**
     * Admins and support can suspend vendors.
     */
    public function suspend(User $user, Vendor $vendor): Response|bool
    {
        if ($user->hasRole('support')) {
            if ($vendor->status !== VendorStatus::Active) {
                return Response::deny('Only active vendors can be suspended.');
            }
            return true;
        }

        return Response::deny('Only admins and support agents can suspend vendors.');
    }

    /**
     * Only the owner can manage bank accounts (highly sensitive).
     */
    public function manageBankAccounts(User $user, Vendor $vendor): Response|bool
    {
        if (! $this->isOwner($user, $vendor)) {
            return Response::deny('Only the store owner can manage bank accounts.');
        }

        if ($vendor->status !== VendorStatus::Active) {
            return Response::deny('Your store must be active to manage bank accounts.');
        }

        return true;
    }

    /**
     * Vendor can view their own payouts.
     * Support can view any vendor's payouts.
     */
    public function viewPayouts(User $user, Vendor $vendor): Response|bool
    {
        if ($this->isOwner($user, $vendor)) {
            return true;
        }

        if ($user->hasRole('support')) {
            return true;
        }

        return Response::deny('You can only view your own payout history.');
    }

    /**
     * Commission rate changes are admin-only.
     */
    public function updateCommission(User $user, Vendor $vendor): Response|bool
    {
        return Response::deny('Only admins can update commission rates.');
    }

    /**
     * Plan changes are admin-only (or via a billing flow).
     */
    public function updatePlan(User $user, Vendor $vendor): Response|bool
    {
        return Response::deny('Plan changes must go through the billing portal.');
    }

    /**
     * Vendors can manage their own translations.
     */
    public function manageTranslations(User $user, Vendor $vendor): Response|bool
    {
        return $this->isOwner($user, $vendor)
            ? Response::allow()
            : Response::deny('You can only manage translations for your own store.');
    }

    /**
     * Vendor analytics — owner and support only.
     */
    public function viewAnalytics(User $user, Vendor $vendor): Response|bool
    {
        if ($this->isOwner($user, $vendor)) {
            return true;
        }

        if ($user->hasRole('support')) {
            return true;
        }

        return Response::deny('You can only view analytics for your own store.');
    }

    // ── Helpers ───────────────────────────────────────────

    private function isOwner(User $user, Vendor $vendor): bool
    {
        return $vendor->owner_user_id === $user->id;
    }
}

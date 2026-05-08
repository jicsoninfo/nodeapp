<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * Authorization rules for Product model actions.
 *
 * Usage in controllers:
 *   $this->authorize('update', $product);
 *
 * Usage in route middleware:
 *   Route::put('/{product}')->can('update', 'product');
 */
class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Admins and moderators bypass all checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole(['admin', 'moderator'])) {
            return true;
        }

        return null; // fall through to individual methods
    }

    /**
     * Any authenticated user can view active products.
     * Vendors can view their own drafts.
     */
    public function view(?User $user, Product $product): Response|bool
    {
        // Public: active products visible to all
        if ($product->status->isVisible()) {
            return true;
        }

        // Unauthenticated can't see non-active products
        if (! $user) return false;

        // Vendors can see their own non-active products
        return $user->vendor?->id === $product->vendor_id
            ? Response::allow()
            : Response::deny('You do not have permission to view this product.');
    }

    /**
     * Only active vendors can create products.
     * Plan-based product limits are checked here.
     */
    public function create(User $user): Response|bool
    {
        $vendor = $user->vendor;

        if (! $vendor || ! $vendor->canSell()) {
            return Response::deny('Your vendor account must be active to create products.');
        }

        $productCount = $vendor->products()->count();
        $maxProducts  = $vendor->plan_type->maxProducts();

        if ($productCount >= $maxProducts) {
            return Response::deny(
                "You have reached your plan limit of {$maxProducts} products. Upgrade your plan to add more."
            );
        }

        return true;
    }

    /**
     * Only the owning vendor can update their product.
     */
    public function update(User $user, Product $product): Response|bool
    {
        return $this->isOwner($user, $product)
            ? Response::allow()
            : Response::deny('You can only edit your own products.');
    }

    /**
     * Only the owning vendor can delete their product.
     * Cannot delete a product that has active/pending orders.
     */
    public function delete(User $user, Product $product): Response|bool
    {
        if (! $this->isOwner($user, $product)) {
            return Response::deny('You can only delete your own products.');
        }

        $hasActiveOrders = \App\Models\OrderItem::whereHas('order', fn ($q) =>
            $q->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])
        )->whereIn('variant_id', $product->variants->pluck('id'))->exists();

        if ($hasActiveOrders) {
            return Response::deny('Cannot delete a product with active orders. Archive it instead.');
        }

        return true;
    }

    /**
     * Only moderators and admins can approve/publish products.
     * (Handled by before() returning true for those roles.)
     */
    public function publish(User $user, Product $product): Response|bool
    {
        return Response::deny('Only admins and moderators can publish products.');
    }

    /**
     * Vendors can manage media on their own products.
     */
    public function manageMedia(User $user, Product $product): Response|bool
    {
        return $this->isOwner($user, $product)
            ? Response::allow()
            : Response::deny('You can only manage media on your own products.');
    }

    /**
     * Vendors can manage variants on their own products.
     */
    public function manageVariants(User $user, Product $product): Response|bool
    {
        return $this->isOwner($user, $product)
            ? Response::allow()
            : Response::deny('You can only manage variants on your own products.');
    }

    // ── Helpers ───────────────────────────────────────────

    private function isOwner(User $user, Product $product): bool
    {
        return $user->vendor?->id === $product->vendor_id;
    }
}

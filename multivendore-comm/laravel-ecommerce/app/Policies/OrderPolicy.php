<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * Authorization rules for Order model actions.
 *
 * Three main actors interact with orders:
 *  1. Buyer     — placed the order, can view/cancel/return
 *  2. Vendor    — fulfills items in the order, can view/ship their items
 *  3. Admin     — full access via before()
 *  4. Support   — read access + cancel/refund via before() partial bypass
 */
class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Admins bypass all checks.
     * Support agents get special partial access handled per method.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Who can view an order:
     * - The buyer who placed it
     * - A vendor who has items in it
     * - Support agents
     */
    public function view(User $user, Order $order): Response|bool
    {
        // Buyer owns the order
        if ($order->user_id === $user->id) {
            return true;
        }

        // Support can view any order
        if ($user->hasRole('support')) {
            return true;
        }

        // Vendor has at least one item in this order
        $vendorId = $user->vendor?->id;
        if ($vendorId && $order->items->where('vendor_id', $vendorId)->isNotEmpty()) {
            return true;
        }

        return Response::deny('You do not have permission to view this order.');
    }

    /**
     * Only the buyer can cancel their own order — and only when status allows.
     * Support agents can also cancel.
     */
    public function cancel(User $user, Order $order): Response|bool
    {
        // Support can cancel any order
        if ($user->hasRole('support')) {
            if (! $order->status->canCancel()) {
                return Response::deny("Order {$order->order_number} cannot be cancelled at this stage.");
            }
            return true;
        }

        // Buyer can cancel their own order
        if ($order->user_id !== $user->id) {
            return Response::deny('You can only cancel your own orders.');
        }

        if (! $order->status->canCancel()) {
            return Response::deny(
                "Order {$order->order_number} is {$order->status->label()} and cannot be cancelled."
            );
        }

        return true;
    }

    /**
     * Refunds can be issued by support agents and admins only.
     */
    public function refund(User $user, Order $order): Response|bool
    {
        if ($user->hasRole(['support', 'admin'])) {
            if ($order->status !== OrderStatus::Delivered && $order->status !== OrderStatus::Cancelled) {
                return Response::deny('Refunds can only be issued on delivered or cancelled orders.');
            }
            return true;
        }

        return Response::deny('Only support agents can issue refunds.');
    }

    /**
     * Buyers can request a return on delivered orders within the return window.
     */
    public function requestReturn(User $user, Order $order): Response|bool
    {
        if ($order->user_id !== $user->id) {
            return Response::deny('You can only request returns on your own orders.');
        }

        if ($order->status !== OrderStatus::Delivered) {
            return Response::deny('Returns can only be requested for delivered orders.');
        }

        // Check if within 30-day return window (platform default)
        $returnWindowDays = 30;
        $deliveredAt      = $order->updated_at; // approximation

        if ($deliveredAt->diffInDays(now()) > $returnWindowDays) {
            return Response::deny(
                "The {$returnWindowDays}-day return window for order {$order->order_number} has expired."
            );
        }

        return true;
    }

    /**
     * Vendors can update the fulfillment status of their own items.
     */
    public function fulfill(User $user, Order $order): Response|bool
    {
        $vendorId = $user->vendor?->id;

        if (! $vendorId) {
            return Response::deny('Only vendors can fulfill orders.');
        }

        $hasItems = $order->items->where('vendor_id', $vendorId)->isNotEmpty();

        return $hasItems
            ? Response::allow()
            : Response::deny('You do not have any items in this order.');
    }

    /**
     * Only the buyer can download their invoice.
     * Support agents can also access invoices.
     */
    public function downloadInvoice(User $user, Order $order): Response|bool
    {
        if ($order->user_id === $user->id) {
            return true;
        }

        if ($user->hasRole('support')) {
            return true;
        }

        return Response::deny('You can only download invoices for your own orders.');
    }

    /**
     * Export permission — support and vendors (own orders only).
     */
    public function export(User $user): Response|bool
    {
        return $user->hasRole(['support', 'vendor'])
            ? true
            : Response::deny('You do not have permission to export orders.');
    }
}

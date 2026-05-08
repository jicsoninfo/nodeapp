<?php

namespace App\Providers;

use App\Events\Order\OrderCancelled;
use App\Events\Order\OrderPlaced;
use App\Events\Order\OrderStatusChanged;
use App\Events\Order\PaymentCaptured;
use App\Events\Product\ProductApproved;
use App\Events\Product\ProductCreated;
use App\Events\Product\ReviewSubmitted;
use App\Events\User\UserRegistered;
use App\Events\User\UserEmailVerified;
use App\Events\Vendor\VendorApproved;
use App\Events\Vendor\VendorSuspended;
use App\Events\Vendor\VendorApplicationReceived;
use App\Listeners\Order\CancelRelatedShipments;
use App\Listeners\Order\NotifyVendorsOnOrderPlaced;
use App\Listeners\Order\ReserveInventoryOnOrder;
use App\Listeners\Order\RestoreInventoryOnCancel;
use App\Listeners\Order\SendOrderCancelledEmail;
use App\Listeners\Order\SendOrderConfirmationEmail;
use App\Listeners\Order\SendOrderStatusPushNotification;
use App\Listeners\Order\TriggerVendorPayoutOnDelivery;
use App\Listeners\Order\UpdateVendorOrderStats;
use App\Listeners\Product\UpdateProductRatingOnReview;
use App\Listeners\Product\NotifyVendorOnNewReview;
use App\Listeners\Product\IndexProductInSearch;
use App\Listeners\User\SendWelcomeEmail;
use App\Listeners\User\CreateUserProfile;
use App\Listeners\User\SendEmailVerifiedNotification;
use App\Listeners\Vendor\SendVendorApprovalEmail;
use App\Listeners\Vendor\SendVendorSuspensionEmail;
use App\Listeners\Vendor\SendVendorApplicationReceivedEmail;
use App\Listeners\Vendor\NotifyAdminOnVendorApplication;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * Listeners that implement ShouldQueue are automatically
     * dispatched to the queue — no extra config needed.
     */
    protected $listen = [

        // ── Laravel Auth ─────────────────────────────────
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        Verified::class => [
            SendEmailVerifiedNotification::class,
        ],

        // ── User ─────────────────────────────────────────
        UserRegistered::class => [
            CreateUserProfile::class,        // Creates default profile record
            SendWelcomeEmail::class,         // Queued — sends welcome email
        ],

        UserEmailVerified::class => [
            SendEmailVerifiedNotification::class,
        ],

        // ── Order ─────────────────────────────────────────
        OrderPlaced::class => [
            ReserveInventoryOnOrder::class,          // Audit inventory reservation
            SendOrderConfirmationEmail::class,       // Queued email to buyer
            NotifyVendorsOnOrderPlaced::class,       // Queued email/push to each vendor
        ],

        PaymentCaptured::class => [
            UpdateVendorOrderStats::class,           // Update vendor dashboard counters
        ],

        OrderStatusChanged::class => [
            SendOrderStatusPushNotification::class,  // Real-time push to buyer
            TriggerVendorPayoutOnDelivery::class,    // Enqueue payout when delivered
        ],

        OrderCancelled::class => [
            RestoreInventoryOnCancel::class,         // Give stock back
            CancelRelatedShipments::class,           // Notify carrier
            SendOrderCancelledEmail::class,          // Email buyer + vendors
        ],

        // ── Product ───────────────────────────────────────
        ProductCreated::class => [
            IndexProductInSearch::class,             // Push to Algolia / Scout
        ],

        ProductApproved::class => [
            IndexProductInSearch::class,
        ],

        ReviewSubmitted::class => [
            UpdateProductRatingOnReview::class,      // Recalculate avg_rating
            NotifyVendorOnNewReview::class,          // Notify vendor of new review
        ],

        // ── Vendor ────────────────────────────────────────
        VendorApplicationReceived::class => [
            SendVendorApplicationReceivedEmail::class, // Confirmation to applicant
            NotifyAdminOnVendorApplication::class,     // Alert admin team
        ],

        VendorApproved::class => [
            SendVendorApprovalEmail::class,          // Approval email to vendor owner
        ],

        VendorSuspended::class => [
            SendVendorSuspensionEmail::class,        // Suspension notice to vendor owner
        ],
    ];

    /**
     * Automatically discover any events & listeners not listed
     * above — searches all Listener classes for handled events.
     */
    protected $discoverEvents = true;

    public function boot(): void {}

    public function shouldDiscoverEvents(): bool
    {
        return false; // Use explicit mapping above for clarity
    }
}

<?php

namespace App\Providers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Vendor;
use App\Policies\CouponPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\VendorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Model → Policy mappings for the application.
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        Order::class   => OrderPolicy::class,
        Vendor::class  => VendorPolicy::class,
        Review::class  => ReviewPolicy::class,
        Coupon::class  => CouponPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

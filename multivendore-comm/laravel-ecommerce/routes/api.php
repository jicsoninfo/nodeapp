<?php

use Illuminate\Support\Facades\Route;

// ============================================================
// API V1 — Multi-vendor SaaS E-Commerce
// ============================================================
// Middleware stack applied per group:
//   forceJson     → always return JSON
//   setLocale     → read Accept-Language header
//   throttle:api  → rate limiting via config/api.php
// ============================================================

Route::prefix('v1')->middleware(['forceJson', 'setLocale'])->group(function () {

    // ──────────────────────────────────────────────────────
    // PUBLIC — no auth required
    // ──────────────────────────────────────────────────────
    Route::prefix('public')->name('public.')->middleware('throttle:120,1')->group(function () {

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/',          [\App\Http\Controllers\Api\V1\Public\ProductController::class, 'index'])->name('index');
            Route::get('/featured',  [\App\Http\Controllers\Api\V1\Public\ProductController::class, 'featured'])->name('featured');
            Route::get('/{product}', [\App\Http\Controllers\Api\V1\Public\ProductController::class, 'show'])->name('show');
        });

        // Categories
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/',               [\App\Http\Controllers\Api\V1\Public\CategoryController::class, 'index'])->name('index');
            Route::get('/tree',           [\App\Http\Controllers\Api\V1\Public\CategoryController::class, 'tree'])->name('tree');
            Route::get('/{category}',     [\App\Http\Controllers\Api\V1\Public\CategoryController::class, 'show'])->name('show');
            Route::get('/{category}/products', [\App\Http\Controllers\Api\V1\Public\CategoryController::class, 'products'])->name('products');
        });

        // Brands
        Route::prefix('brands')->name('brands.')->group(function () {
            Route::get('/',         [\App\Http\Controllers\Api\V1\Public\BrandController::class, 'index'])->name('index');
            Route::get('/{brand}',  [\App\Http\Controllers\Api\V1\Public\BrandController::class, 'show'])->name('show');
        });

        // Vendors (storefront)
        Route::prefix('stores')->name('stores.')->group(function () {
            Route::get('/',                      [\App\Http\Controllers\Api\V1\Public\StoreController::class, 'index'])->name('index');
            Route::get('/{slug}',                [\App\Http\Controllers\Api\V1\Public\StoreController::class, 'show'])->name('show');
            Route::get('/{slug}/products',       [\App\Http\Controllers\Api\V1\Public\StoreController::class, 'products'])->name('products');
            Route::get('/{slug}/reviews',        [\App\Http\Controllers\Api\V1\Public\StoreController::class, 'reviews'])->name('reviews');
        });

        // Reviews (read-only public)
        Route::get('/products/{product}/reviews', [\App\Http\Controllers\Api\V1\Public\ReviewController::class, 'index'])->name('reviews.index');

        // Search
        Route::get('/search', \App\Http\Controllers\Api\V1\Public\SearchController::class)->name('search');

        // Languages & Currencies
        Route::get('/languages',  [\App\Http\Controllers\Api\V1\Public\LocaleController::class, 'languages'])->name('languages');
        Route::get('/currencies', [\App\Http\Controllers\Api\V1\Public\LocaleController::class, 'currencies'])->name('currencies');

        // Exchange Rates
        Route::get('/exchange-rates',          [\App\Http\Controllers\Api\V1\Public\LocaleController::class, 'exchangeRates'])->name('exchange-rates');
        Route::get('/exchange-rates/{from}/{to}', [\App\Http\Controllers\Api\V1\Public\LocaleController::class, 'rate'])->name('exchange-rates.pair');
    });

    // ──────────────────────────────────────────────────────
    // AUTH — register, login, verify
    // ──────────────────────────────────────────────────────
    Route::prefix('auth')->name('auth.')->middleware('throttle:20,1')->group(function () {

        // Registration & Login
        Route::post('/register', \App\Http\Controllers\Api\V1\Auth\RegisterController::class)->name('register');
        Route::post('/login',    \App\Http\Controllers\Api\V1\Auth\LoginController::class)->name('login');

        // Social OAuth
        Route::post('/social/{provider}', [\App\Http\Controllers\Api\V1\Auth\SocialAuthController::class, 'handleCallback'])->name('social');

        // Email Verification
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/email/verify',           [\App\Http\Controllers\Api\V1\Auth\EmailVerificationController::class, 'send'])->name('verification.send');
            Route::get('/email/verify/{id}/{hash}',[\App\Http\Controllers\Api\V1\Auth\EmailVerificationController::class, 'verify'])
                ->middleware('signed')
                ->name('verification.verify');
        });

        // Password Reset
        Route::post('/password/forgot', [\App\Http\Controllers\Api\V1\Auth\PasswordResetController::class, 'sendLink'])->name('password.email');
        Route::post('/password/reset',  [\App\Http\Controllers\Api\V1\Auth\PasswordResetController::class, 'reset'])->name('password.reset');

        // Logout (requires auth)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout',        [\App\Http\Controllers\Api\V1\Auth\LogoutController::class, 'logout'])->name('logout');
            Route::post('/logout/all',    [\App\Http\Controllers\Api\V1\Auth\LogoutController::class, 'logoutAll'])->name('logout.all');
            Route::post('/refresh-token', [\App\Http\Controllers\Api\V1\Auth\RefreshTokenController::class, '__invoke'])->name('refresh');
        });
    });

    // ──────────────────────────────────────────────────────
    // BUYER — authenticated customer routes
    // ──────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('buyer')->name('buyer.')->group(function () {

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/',               [\App\Http\Controllers\Api\V1\Buyer\ProfileController::class, 'show'])->name('show');
            Route::put('/',               [\App\Http\Controllers\Api\V1\Buyer\ProfileController::class, 'update'])->name('update');
            Route::post('/avatar',        [\App\Http\Controllers\Api\V1\Buyer\ProfileController::class, 'uploadAvatar'])->name('avatar');
            Route::put('/password',       [\App\Http\Controllers\Api\V1\Buyer\ProfileController::class, 'changePassword'])->name('password');
            Route::delete('/',            [\App\Http\Controllers\Api\V1\Buyer\ProfileController::class, 'deleteAccount'])->name('delete');
        });

        // Addresses
        Route::apiResource('addresses', \App\Http\Controllers\Api\V1\Buyer\AddressController::class);
        Route::post('/addresses/{address}/default', [\App\Http\Controllers\Api\V1\Buyer\AddressController::class, 'setDefault'])->name('addresses.default');

        // Cart
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/',                           [\App\Http\Controllers\Api\V1\Buyer\CartController::class, 'show'])->name('show');
            Route::post('/items',                     [\App\Http\Controllers\Api\V1\Buyer\CartController::class, 'addItem'])->name('items.add');
            Route::patch('/items/{cartItem}',         [\App\Http\Controllers\Api\V1\Buyer\CartController::class, 'updateItem'])->name('items.update');
            Route::delete('/items/{cartItem}',        [\App\Http\Controllers\Api\V1\Buyer\CartController::class, 'removeItem'])->name('items.remove');
            Route::post('/coupon',                    [\App\Http\Controllers\Api\V1\Buyer\CartController::class, 'applyCoupon'])->name('coupon.apply');
            Route::delete('/coupon',                  [\App\Http\Controllers\Api\V1\Buyer\CartController::class, 'removeCoupon'])->name('coupon.remove');
            Route::delete('/',                        [\App\Http\Controllers\Api\V1\Buyer\CartController::class, 'clear'])->name('clear');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/',                               [\App\Http\Controllers\Api\V1\Buyer\OrderController::class, 'index'])->name('index');
            Route::post('/',                              [\App\Http\Controllers\Api\V1\Buyer\OrderController::class, 'store'])->name('store');
            Route::get('/{order}',                        [\App\Http\Controllers\Api\V1\Buyer\OrderController::class, 'show'])->name('show');
            Route::post('/{order}/cancel',                [\App\Http\Controllers\Api\V1\Buyer\OrderController::class, 'cancel'])->name('cancel');
            Route::post('/{order}/return',                [\App\Http\Controllers\Api\V1\Buyer\OrderController::class, 'requestReturn'])->name('return');
            Route::get('/{order}/invoice',                [\App\Http\Controllers\Api\V1\Buyer\OrderController::class, 'invoice'])->name('invoice');
            Route::get('/{order}/tracking',               [\App\Http\Controllers\Api\V1\Buyer\OrderController::class, 'tracking'])->name('tracking');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::post('/intent',         [\App\Http\Controllers\Api\V1\Buyer\PaymentController::class, 'createIntent'])->name('intent');
            Route::post('/confirm',        [\App\Http\Controllers\Api\V1\Buyer\PaymentController::class, 'confirm'])->name('confirm');
            Route::get('/methods',         [\App\Http\Controllers\Api\V1\Buyer\PaymentController::class, 'methods'])->name('methods');
        });

        // Reviews
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/',                   [\App\Http\Controllers\Api\V1\Buyer\ReviewController::class, 'index'])->name('index');
            Route::post('/',                  [\App\Http\Controllers\Api\V1\Buyer\ReviewController::class, 'store'])->name('store');
            Route::put('/{review}',           [\App\Http\Controllers\Api\V1\Buyer\ReviewController::class, 'update'])->name('update');
            Route::delete('/{review}',        [\App\Http\Controllers\Api\V1\Buyer\ReviewController::class, 'destroy'])->name('destroy');
            Route::post('/{review}/helpful',  [\App\Http\Controllers\Api\V1\Buyer\ReviewController::class, 'markHelpful'])->name('helpful');
        });

        // Wishlists
        Route::apiResource('wishlists', \App\Http\Controllers\Api\V1\Buyer\WishlistController::class);
        Route::post('/wishlists/{wishlist}/items',          [\App\Http\Controllers\Api\V1\Buyer\WishlistController::class, 'addItem'])->name('wishlists.items.add');
        Route::delete('/wishlists/{wishlist}/items/{item}', [\App\Http\Controllers\Api\V1\Buyer\WishlistController::class, 'removeItem'])->name('wishlists.items.remove');
        Route::post('/wishlists/{wishlist}/move-to-cart',   [\App\Http\Controllers\Api\V1\Buyer\WishlistController::class, 'moveToCart'])->name('wishlists.move-to-cart');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/',                   [\App\Http\Controllers\Api\V1\Buyer\NotificationController::class, 'index'])->name('index');
            Route::patch('/{id}/read',        [\App\Http\Controllers\Api\V1\Buyer\NotificationController::class, 'markRead'])->name('read');
            Route::post('/read-all',          [\App\Http\Controllers\Api\V1\Buyer\NotificationController::class, 'readAll'])->name('read-all');
            Route::delete('/{id}',            [\App\Http\Controllers\Api\V1\Buyer\NotificationController::class, 'destroy'])->name('destroy');
        });

        // Search History
        Route::get('/search-history',         [\App\Http\Controllers\Api\V1\Buyer\SearchHistoryController::class, 'index'])->name('search-history.index');
        Route::delete('/search-history',      [\App\Http\Controllers\Api\V1\Buyer\SearchHistoryController::class, 'clear'])->name('search-history.clear');
    });

    // ──────────────────────────────────────────────────────
    // VENDOR — authenticated seller routes
    // ──────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'role:vendor', 'vendor.active', 'throttle:api'])
        ->prefix('vendor')
        ->name('vendor.')
        ->group(function () {

        // Dashboard
        Route::get('/dashboard', \App\Http\Controllers\Api\V1\Vendor\DashboardController::class)->name('dashboard');

        // Store profile
        Route::prefix('store')->name('store.')->group(function () {
            Route::get('/',            [\App\Http\Controllers\Api\V1\Vendor\StoreController::class, 'show'])->name('show');
            Route::put('/',            [\App\Http\Controllers\Api\V1\Vendor\StoreController::class, 'update'])->name('update');
            Route::post('/logo',       [\App\Http\Controllers\Api\V1\Vendor\StoreController::class, 'uploadLogo'])->name('logo');
            Route::post('/banner',     [\App\Http\Controllers\Api\V1\Vendor\StoreController::class, 'uploadBanner'])->name('banner');
        });

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/',                        [\App\Http\Controllers\Api\V1\Vendor\ProductController::class, 'index'])->name('index');
            Route::post('/',                       [\App\Http\Controllers\Api\V1\Vendor\ProductController::class, 'store'])->name('store');
            Route::get('/{product}',               [\App\Http\Controllers\Api\V1\Vendor\ProductController::class, 'show'])->name('show');
            Route::put('/{product}',               [\App\Http\Controllers\Api\V1\Vendor\ProductController::class, 'update'])->name('update');
            Route::delete('/{product}',            [\App\Http\Controllers\Api\V1\Vendor\ProductController::class, 'destroy'])->name('destroy');
            Route::patch('/{product}/status',      [\App\Http\Controllers\Api\V1\Vendor\ProductController::class, 'updateStatus'])->name('status');

            // Variants
            Route::prefix('/{product}/variants')->name('variants.')->group(function () {
                Route::get('/',              [\App\Http\Controllers\Api\V1\Vendor\VariantController::class, 'index'])->name('index');
                Route::post('/',             [\App\Http\Controllers\Api\V1\Vendor\VariantController::class, 'store'])->name('store');
                Route::put('/{variant}',     [\App\Http\Controllers\Api\V1\Vendor\VariantController::class, 'update'])->name('update');
                Route::delete('/{variant}',  [\App\Http\Controllers\Api\V1\Vendor\VariantController::class, 'destroy'])->name('destroy');
            });

            // Media
            Route::prefix('/{product}/media')->name('media.')->group(function () {
                Route::get('/',              [\App\Http\Controllers\Api\V1\Vendor\ProductMediaController::class, 'index'])->name('index');
                Route::post('/',             [\App\Http\Controllers\Api\V1\Vendor\ProductMediaController::class, 'store'])->name('store');
                Route::delete('/{media}',    [\App\Http\Controllers\Api\V1\Vendor\ProductMediaController::class, 'destroy'])->name('destroy');
                Route::post('/reorder',      [\App\Http\Controllers\Api\V1\Vendor\ProductMediaController::class, 'reorder'])->name('reorder');
            });
        });

        // Inventory
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/',                    [\App\Http\Controllers\Api\V1\Vendor\InventoryController::class, 'index'])->name('index');
            Route::patch('/{variant}/stock',   [\App\Http\Controllers\Api\V1\Vendor\InventoryController::class, 'updateStock'])->name('stock');
            Route::post('/bulk-update',        [\App\Http\Controllers\Api\V1\Vendor\InventoryController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/low-stock',           [\App\Http\Controllers\Api\V1\Vendor\InventoryController::class, 'lowStock'])->name('low-stock');
            Route::get('/export',              [\App\Http\Controllers\Api\V1\Vendor\InventoryController::class, 'export'])->name('export');
        });

        // Orders (vendor view)
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/',                        [\App\Http\Controllers\Api\V1\Vendor\OrderController::class, 'index'])->name('index');
            Route::get('/{order}',                 [\App\Http\Controllers\Api\V1\Vendor\OrderController::class, 'show'])->name('show');
            Route::patch('/{orderItem}/fulfill',   [\App\Http\Controllers\Api\V1\Vendor\OrderController::class, 'markFulfilled'])->name('fulfill');
            Route::post('/{orderItem}/shipment',   [\App\Http\Controllers\Api\V1\Vendor\OrderController::class, 'addShipment'])->name('shipment');
            Route::get('/export',                  [\App\Http\Controllers\Api\V1\Vendor\OrderController::class, 'export'])->name('export');
        });

        // Reviews (vendor view & reply)
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/',                       [\App\Http\Controllers\Api\V1\Vendor\ReviewController::class, 'index'])->name('index');
            Route::post('/{review}/reply',        [\App\Http\Controllers\Api\V1\Vendor\ReviewController::class, 'reply'])->name('reply');
        });

        // Payouts
        Route::prefix('payouts')->name('payouts.')->group(function () {
            Route::get('/',         [\App\Http\Controllers\Api\V1\Vendor\PayoutController::class, 'index'])->name('index');
            Route::get('/pending',  [\App\Http\Controllers\Api\V1\Vendor\PayoutController::class, 'pending'])->name('pending');
            Route::get('/{payout}', [\App\Http\Controllers\Api\V1\Vendor\PayoutController::class, 'show'])->name('show');
        });

        // Bank Accounts
        Route::prefix('bank-accounts')->name('bank-accounts.')->group(function () {
            Route::get('/',                       [\App\Http\Controllers\Api\V1\Vendor\BankAccountController::class, 'index'])->name('index');
            Route::post('/',                      [\App\Http\Controllers\Api\V1\Vendor\BankAccountController::class, 'store'])->name('store');
            Route::delete('/{account}',           [\App\Http\Controllers\Api\V1\Vendor\BankAccountController::class, 'destroy'])->name('destroy');
            Route::patch('/{account}/primary',    [\App\Http\Controllers\Api\V1\Vendor\BankAccountController::class, 'setPrimary'])->name('primary');
        });

        // Policies (return / shipping / warranty)
        Route::apiResource('policies', \App\Http\Controllers\Api\V1\Vendor\PolicyController::class);

        // Analytics
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/revenue',      [\App\Http\Controllers\Api\V1\Vendor\AnalyticsController::class, 'revenue'])->name('revenue');
            Route::get('/orders',       [\App\Http\Controllers\Api\V1\Vendor\AnalyticsController::class, 'orders'])->name('orders');
            Route::get('/products',     [\App\Http\Controllers\Api\V1\Vendor\AnalyticsController::class, 'products'])->name('products');
            Route::get('/customers',    [\App\Http\Controllers\Api\V1\Vendor\AnalyticsController::class, 'customers'])->name('customers');
            Route::get('/overview',     [\App\Http\Controllers\Api\V1\Vendor\AnalyticsController::class, 'overview'])->name('overview');
        });

        // Coupons (vendor-scoped)
        Route::apiResource('coupons', \App\Http\Controllers\Api\V1\Vendor\CouponController::class);
        Route::patch('/coupons/{coupon}/toggle', [\App\Http\Controllers\Api\V1\Vendor\CouponController::class, 'toggle'])->name('coupons.toggle');

        // Translations
        Route::prefix('translations')->name('translations.')->group(function () {
            Route::get('/',         [\App\Http\Controllers\Api\V1\Vendor\TranslationController::class, 'index'])->name('index');
            Route::put('/',         [\App\Http\Controllers\Api\V1\Vendor\TranslationController::class, 'update'])->name('update');
        });

        // Notifications (vendor-specific)
        Route::get('/notifications', [\App\Http\Controllers\Api\V1\Vendor\NotificationController::class, 'index'])->name('notifications.index');
    });

    // ──────────────────────────────────────────────────────
    // ADMIN — platform superadmin routes
    // ──────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'role:admin', 'throttle:200,1'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        // Platform Dashboard
        Route::get('/dashboard', \App\Http\Controllers\Api\V1\Admin\DashboardController::class)->name('dashboard');

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',                       [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'index'])->name('index');
            Route::get('/{user}',                 [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'show'])->name('show');
            Route::patch('/{user}/status',        [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'updateStatus'])->name('status');
            Route::patch('/{user}/role',          [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'assignRole'])->name('role');
            Route::post('/{user}/impersonate',    [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'impersonate'])->name('impersonate');
            Route::delete('/{user}',              [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'destroy'])->name('destroy');
        });

        // Vendors
        Route::prefix('vendors')->name('vendors.')->group(function () {
            Route::get('/',                      [\App\Http\Controllers\Api\V1\Admin\VendorController::class, 'index'])->name('index');
            Route::get('/{vendor}',              [\App\Http\Controllers\Api\V1\Admin\VendorController::class, 'show'])->name('show');
            Route::post('/{vendor}/approve',     [\App\Http\Controllers\Api\V1\Admin\VendorController::class, 'approve'])->name('approve');
            Route::post('/{vendor}/reject',      [\App\Http\Controllers\Api\V1\Admin\VendorController::class, 'reject'])->name('reject');
            Route::post('/{vendor}/suspend',     [\App\Http\Controllers\Api\V1\Admin\VendorController::class, 'suspend'])->name('suspend');
            Route::patch('/{vendor}/commission', [\App\Http\Controllers\Api\V1\Admin\VendorController::class, 'updateCommission'])->name('commission');
            Route::patch('/{vendor}/plan',       [\App\Http\Controllers\Api\V1\Admin\VendorController::class, 'updatePlan'])->name('plan');
        });

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/',                     [\App\Http\Controllers\Api\V1\Admin\ProductController::class, 'index'])->name('index');
            Route::get('/{product}',            [\App\Http\Controllers\Api\V1\Admin\ProductController::class, 'show'])->name('show');
            Route::patch('/{product}/status',   [\App\Http\Controllers\Api\V1\Admin\ProductController::class, 'updateStatus'])->name('status');
            Route::delete('/{product}',         [\App\Http\Controllers\Api\V1\Admin\ProductController::class, 'destroy'])->name('destroy');
        });

        // Categories (full CRUD)
        Route::apiResource('categories', \App\Http\Controllers\Api\V1\Admin\CategoryController::class);
        Route::post('/categories/reorder', [\App\Http\Controllers\Api\V1\Admin\CategoryController::class, 'reorder'])->name('categories.reorder');

        // Brands
        Route::apiResource('brands', \App\Http\Controllers\Api\V1\Admin\BrandController::class);
        Route::patch('/brands/{brand}/verify', [\App\Http\Controllers\Api\V1\Admin\BrandController::class, 'verify'])->name('brands.verify');

        // Attributes
        Route::apiResource('attributes',          \App\Http\Controllers\Api\V1\Admin\AttributeController::class);
        Route::apiResource('attributes.values',   \App\Http\Controllers\Api\V1\Admin\AttributeValueController::class)->shallow();

        // Orders (admin view)
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/',                      [\App\Http\Controllers\Api\V1\Admin\OrderController::class, 'index'])->name('index');
            Route::get('/{order}',               [\App\Http\Controllers\Api\V1\Admin\OrderController::class, 'show'])->name('show');
            Route::patch('/{order}/status',      [\App\Http\Controllers\Api\V1\Admin\OrderController::class, 'updateStatus'])->name('status');
            Route::post('/{order}/refund',       [\App\Http\Controllers\Api\V1\Admin\OrderController::class, 'refund'])->name('refund');
            Route::get('/export',                [\App\Http\Controllers\Api\V1\Admin\OrderController::class, 'export'])->name('export');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/',          [\App\Http\Controllers\Api\V1\Admin\PaymentController::class, 'index'])->name('index');
            Route::get('/{payment}', [\App\Http\Controllers\Api\V1\Admin\PaymentController::class, 'show'])->name('show');
            Route::post('/refund',   [\App\Http\Controllers\Api\V1\Admin\PaymentController::class, 'refund'])->name('refund');
        });

        // Payouts
        Route::prefix('payouts')->name('payouts.')->group(function () {
            Route::get('/',                      [\App\Http\Controllers\Api\V1\Admin\PayoutController::class, 'index'])->name('index');
            Route::post('/process-batch',        [\App\Http\Controllers\Api\V1\Admin\PayoutController::class, 'processBatch'])->name('process-batch');
            Route::post('/{payout}/process',     [\App\Http\Controllers\Api\V1\Admin\PayoutController::class, 'process'])->name('process');
        });

        // Coupons (platform-wide)
        Route::apiResource('coupons', \App\Http\Controllers\Api\V1\Admin\CouponController::class);

        // Reviews moderation
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/',                      [\App\Http\Controllers\Api\V1\Admin\ReviewController::class, 'index'])->name('index');
            Route::patch('/{review}/approve',    [\App\Http\Controllers\Api\V1\Admin\ReviewController::class, 'approve'])->name('approve');
            Route::patch('/{review}/reject',     [\App\Http\Controllers\Api\V1\Admin\ReviewController::class, 'reject'])->name('reject');
            Route::delete('/{review}',           [\App\Http\Controllers\Api\V1\Admin\ReviewController::class, 'destroy'])->name('destroy');
        });

        // Languages & Currencies management
        Route::apiResource('languages',  \App\Http\Controllers\Api\V1\Admin\LanguageController::class);
        Route::apiResource('currencies', \App\Http\Controllers\Api\V1\Admin\CurrencyController::class);
        Route::post('/exchange-rates/sync', [\App\Http\Controllers\Api\V1\Admin\CurrencyController::class, 'syncRates'])->name('exchange-rates.sync');

        // Analytics (platform-level)
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/overview',     [\App\Http\Controllers\Api\V1\Admin\AnalyticsController::class, 'overview'])->name('overview');
            Route::get('/revenue',      [\App\Http\Controllers\Api\V1\Admin\AnalyticsController::class, 'revenue'])->name('revenue');
            Route::get('/users',        [\App\Http\Controllers\Api\V1\Admin\AnalyticsController::class, 'users'])->name('users');
            Route::get('/vendors',      [\App\Http\Controllers\Api\V1\Admin\AnalyticsController::class, 'vendors'])->name('vendors');
            Route::get('/products',     [\App\Http\Controllers\Api\V1\Admin\AnalyticsController::class, 'products'])->name('products');
            Route::get('/search',       [\App\Http\Controllers\Api\V1\Admin\AnalyticsController::class, 'search'])->name('search');
        });

        // Notifications (broadcast to users)
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::post('/broadcast',   [\App\Http\Controllers\Api\V1\Admin\NotificationController::class, 'broadcast'])->name('broadcast');
            Route::post('/user/{user}', [\App\Http\Controllers\Api\V1\Admin\NotificationController::class, 'sendToUser'])->name('user');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/',     [\App\Http\Controllers\Api\V1\Admin\SettingController::class, 'index'])->name('index');
            Route::put('/',     [\App\Http\Controllers\Api\V1\Admin\SettingController::class, 'update'])->name('update');
        });

        // Activity Log
        Route::get('/activity-log', [\App\Http\Controllers\Api\V1\Admin\ActivityLogController::class, 'index'])->name('activity-log');
    });

    // ──────────────────────────────────────────────────────
    // WEBHOOKS — payment provider callbacks (no auth, signed)
    // ──────────────────────────────────────────────────────
    Route::prefix('webhooks')->name('webhooks.')->middleware('throttle:300,1')->group(function () {
        Route::post('/stripe',    [\App\Http\Controllers\Api\V1\Webhook\StripeWebhookController::class,    '__invoke'])->name('stripe');
        Route::post('/razorpay',  [\App\Http\Controllers\Api\V1\Webhook\RazorpayWebhookController::class,  '__invoke'])->name('razorpay');
    });

    // ──────────────────────────────────────────────────────
    // VENDOR ONBOARDING — partially authed (email verified)
    // ──────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'verified'])->prefix('onboarding')->name('onboarding.')->group(function () {
        Route::post('/vendor/apply',   [\App\Http\Controllers\Api\V1\Onboarding\VendorApplicationController::class, 'apply'])->name('vendor.apply');
        Route::get('/vendor/status',   [\App\Http\Controllers\Api\V1\Onboarding\VendorApplicationController::class, 'status'])->name('vendor.status');
    });

}); // end v1

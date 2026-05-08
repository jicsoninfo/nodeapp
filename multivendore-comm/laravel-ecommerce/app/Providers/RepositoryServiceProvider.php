<?php

namespace App\Providers;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\VendorRepository;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository interface → implementation bindings.
     * Add new pairs here as the app grows — never hardcode
     * concrete classes in controllers or services.
     */
    public array $bindings = [
        // Repositories
        ProductRepositoryInterface::class => ProductRepository::class,
        OrderRepositoryInterface::class   => OrderRepository::class,
        VendorRepositoryInterface::class  => VendorRepository::class,

        // Services
        CartServiceInterface::class    => CartService::class,
        OrderServiceInterface::class   => OrderService::class,
        PaymentServiceInterface::class => PaymentService::class,
    ];

    public function register(): void
    {
        // All bindings are handled via the $bindings array above.
        // Add singletons here for stateful services.

        $this->app->singleton(\App\Services\CurrencyService::class);
        $this->app->singleton(\App\Services\VendorPayoutService::class);
        $this->app->singleton(\App\Services\NotificationService::class);
        $this->app->singleton(\App\Services\SearchService::class);
        $this->app->singleton(\App\Services\MediaService::class);
    }

    public function boot(): void {}
}

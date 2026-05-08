<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Vendor;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\ReviewObserver;
use App\Observers\VendorObserver;
use App\Support\Macros\QueryBuilderMacros;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register Telescope only in local/dev
        if ($this->app->environment('local', 'development')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        $this->configureModels();
        $this->configureRateLimiting();
        $this->configureUrls();
        $this->configureResources();
        $this->registerObservers();
        $this->configureQueryLogging();
    }

    // ── Model configuration ───────────────────────────────
    private function configureModels(): void
    {
        // Prevent lazy loading in non-production to catch N+1 queries early
        Model::preventLazyLoading(! $this->app->isProduction());

        // Prevent silently discarding unfillable attributes
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        // Prevent accessing missing attributes
        Model::preventAccessingMissingAttributes(! $this->app->isProduction());
    }

    // ── Rate Limiting ─────────────────────────────────────
    private function configureRateLimiting(): void
    {
        // Default API limiter
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(config('api.rate_limit', 60))->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        // Auth endpoints — strict limiting to prevent brute force
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Search — generous for public users
        RateLimiter::for('search', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        // Webhooks — high limits, verified by signature
        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::perMinute(300)->by($request->ip());
        });
    }

    // ── URLs ──────────────────────────────────────────────
    private function configureUrls(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }

    // ── API Resources ─────────────────────────────────────
    private function configureResources(): void
    {
        // Remove the outer `data` key wrapping from all collections
        JsonResource::withoutWrapping();
    }

    // ── Observers ─────────────────────────────────────────
    private function registerObservers(): void
    {
        Product::observe(ProductObserver::class);
        Order::observe(OrderObserver::class);
        Review::observe(ReviewObserver::class);
        Vendor::observe(VendorObserver::class);
    }

    // ── Query Logging (local only) ────────────────────────
    private function configureQueryLogging(): void
    {
        if ($this->app->environment('local') && config('app.debug')) {
            DB::listen(function ($query) {
                if ($query->time > 500) {
                    Log::warning('Slow query detected', [
                        'sql'      => $query->sql,
                        'bindings' => $query->bindings,
                        'time_ms'  => $query->time,
                    ]);
                }
            });
        }
    }
}

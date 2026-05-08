<?php

use App\Http\Middleware\EnsureVendorIsActive;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global API middleware
        $middleware->api(prepend: [
            ForceJsonResponse::class,
            SetLocale::class,
        ]);

        // Named middleware aliases
        $middleware->alias([
            'vendor.active' => EnsureVendorIsActive::class,
            'role'          => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'    => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Throttle groups
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Exceptions are handled in App\Exceptions\Handler
    })
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
    ])
    ->create();

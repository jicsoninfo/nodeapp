<?php
use Illuminate\Support\Facades\Route;

// Health check (load balancer / uptime monitor)
Route::get('/health', function () {
    return response()->json([
        'status'    => 'ok',
        'app'       => config('app.name'),
        'env'       => config('app.env'),
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health');

// Horizon dashboard (admin only in production)
Route::get('/horizon', function () {
    return redirect('/horizon/dashboard');
})->name('horizon');

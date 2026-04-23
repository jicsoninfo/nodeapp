<?php

use App\Http\Controllers\ProductController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// All shop routes are prefixed with locale: /en/products, /ar/products, etc.
Route::prefix('{locale}')
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('products',        [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{slug}', [ProductController::class, 'show'])->name('products.show');
    });

// Redirect root to default locale
Route::get('/', function () {
    return redirect('/' . app()->getLocale() . '/products');
});

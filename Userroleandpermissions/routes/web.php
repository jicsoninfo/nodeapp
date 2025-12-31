<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController as authproductcontroller;

use App\Http\Controllers\Cart\ProductController;



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
  
Route::get('/home', [HomeController::class, 'index'])->name('home');
  
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', authproductcontroller::class);
});


Route::get('/', [ProductController::class, 'index']);  
Route::get('cart', [ProductController::class, 'cart'])->name('cart');
Route::get('add-to-cart/{id}', [ProductController::class, 'addToCart'])->name('add.to.cart');
Route::patch('update-cart', [ProductController::class, 'update'])->name('update.cart');
Route::delete('remove-from-cart', [ProductController::class, 'remove'])->name('remove.from.cart');



/*

composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
php artisan make:migration create_products_table
php artisan migrate


composer require laravel/ui



1.role-list

2.role-create

3.role-edit

4.role-delete

5.product-list

6.product-create

7.product-edit

8.product-delete

So, first create a seeder using the below command:

php artisan make:seeder PermissionTableSeeder

php artisan db:seed --class=PermissionTableSeeder

php artisan make:seeder CreateAdminUserSeeder
php artisan db:seed --class=CreateAdminUserSeeder

Email: admin@gmail.com
Password: 123456



php artisan migrate --path=/database/migrations/2025_05_29_102646_create_products_table.php

php artisan make:model cartProduct

php artisan make:seed ProductSeeder

php artisan db:seed --class=cartProductSeeder

*/
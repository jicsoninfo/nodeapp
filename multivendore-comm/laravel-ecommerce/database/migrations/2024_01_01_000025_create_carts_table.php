<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id')->nullable();
            $table->string('session_id', 128)->nullable();
            $table->uuid('coupon_id')->nullable();
            $table->char('currency', 3)->default('USD');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('user_id');
            $table->index('session_id');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('coupon_id')->references('id')->on('coupons')->nullOnDelete();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('cart_id');
            $table->uuid('variant_id');
            $table->uuid('vendor_id');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->timestamps();

            $table->unique(['cart_id', 'variant_id']);

            $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            $table->foreign('variant_id')->references('id')->on('product_variants');
            $table->foreign('vendor_id')->references('id')->on('vendors');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id');
            $table->string('name', 200)->default('My Wishlist');
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('wishlist_id');
            $table->uuid('variant_id');
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();

            $table->unique(['wishlist_id', 'variant_id']);

            $table->foreign('wishlist_id')->references('id')->on('wishlists')->cascadeOnDelete();
            $table->foreign('variant_id')->references('id')->on('product_variants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('wishlists');
    }
};

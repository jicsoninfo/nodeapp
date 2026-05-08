<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('product_id');
            $table->uuid('user_id');
            $table->uuid('order_item_id')->nullable();
            $table->tinyInteger('rating')->unsigned();
            $table->string('title', 255)->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->unsignedInteger('helpful_votes')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'hidden'])->default('pending');
            $table->string('lang_code', 10)->default('en');
            $table->timestamps();

            $table->unique(['product_id', 'user_id']);
            $table->index('product_id');
            $table->index('status');
            $table->index('rating');

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('order_item_id')->references('id')->on('order_items')->nullOnDelete();
        });

        Schema::create('review_media', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('review_id');
            $table->string('url', 1000);
            $table->enum('type', ['image', 'video'])->default('image');
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('reviews')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_media');
        Schema::dropIfExists('reviews');
    }
};

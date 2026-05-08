<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('product_id');
            $table->uuid('variant_id')->nullable();
            $table->string('url', 1000);
            $table->enum('type', ['image', 'video', '360'])->default('image');
            $table->string('alt_text', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('product_id');
            $table->index('variant_id');

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('product_media'); }
};

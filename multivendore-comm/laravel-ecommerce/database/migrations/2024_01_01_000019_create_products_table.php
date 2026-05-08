<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('vendor_id');
            $table->uuid('category_id')->nullable();
            $table->uuid('brand_id')->nullable();
            $table->string('asin', 20)->unique();
            $table->enum('status', ['draft', 'active', 'inactive', 'archived'])->default('draft');
            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('status');
            $table->index('avg_rating');

            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->nullOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('products'); }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_profiles', function (Blueprint $table) {
            $table->uuid('vendor_id')->primary();
            $table->text('description')->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('banner_url', 500)->nullable();
            $table->enum('business_type', ['individual', 'company', 'brand'])->default('individual');
            $table->string('tax_id', 100)->nullable();
            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->string('website_url', 500)->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('vendor_profiles'); }
};

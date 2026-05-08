<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->uuid('variant_id');
            $table->uuid('attribute_id');
            $table->uuid('attribute_value_id');

            $table->primary(['variant_id', 'attribute_id']);

            $table->foreign('variant_id')->references('id')->on('product_variants')->cascadeOnDelete();
            $table->foreign('attribute_id')->references('id')->on('attributes');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values');
        });
    }

    public function down(): void { Schema::dropIfExists('variant_attributes'); }
};

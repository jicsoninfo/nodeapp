<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('order_id');
            $table->uuid('vendor_id');
            $table->uuid('variant_id');
            $table->unsignedSmallInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->enum('fulfillment_status', [
                'pending', 'processing', 'shipped',
                'delivered', 'returned', 'cancelled',
            ])->default('pending');
            $table->timestamps();

            $table->index('order_id');
            $table->index('vendor_id');
            $table->index('fulfillment_status');

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('variant_id')->references('id')->on('product_variants');
        });
    }

    public function down(): void { Schema::dropIfExists('order_items'); }
};

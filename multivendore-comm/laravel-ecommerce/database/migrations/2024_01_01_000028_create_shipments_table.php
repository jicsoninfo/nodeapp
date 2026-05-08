<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('order_item_id');
            $table->string('carrier', 100);
            $table->string('tracking_number', 200)->nullable();
            $table->enum('status', [
                'label_created', 'picked_up', 'in_transit',
                'out_for_delivery', 'delivered', 'failed', 'returned',
            ])->default('label_created');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('estimated_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index('order_item_id');
            $table->index('tracking_number');

            $table->foreign('order_item_id')->references('id')->on('order_items')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('shipments'); }
};

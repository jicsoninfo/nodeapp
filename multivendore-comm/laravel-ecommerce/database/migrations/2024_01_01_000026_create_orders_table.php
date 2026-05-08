<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id');
            $table->uuid('address_id');
            $table->uuid('coupon_id')->nullable();
            $table->string('order_number', 30)->unique();
            $table->enum('status', [
                'pending', 'confirmed', 'processing',
                'shipped', 'delivered', 'cancelled', 'refunded',
            ])->default('pending');
            $table->char('currency', 3)->default('USD');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('shipping_amount', 12, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->useCurrent();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('placed_at');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->foreign('coupon_id')->references('id')->on('coupons')->nullOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('orders'); }
};

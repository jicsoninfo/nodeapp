<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('order_id');
            $table->enum('method', ['card', 'upi', 'netbanking', 'wallet', 'cod', 'bnpl', 'crypto']);
            $table->string('provider', 100);
            $table->string('provider_txn_id', 255)->nullable();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->enum('status', [
                'pending', 'authorised', 'captured',
                'failed', 'refunded', 'partially_refunded',
            ])->default('pending');
            $table->json('meta')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('status');
            $table->index('provider_txn_id');

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('payments'); }
};

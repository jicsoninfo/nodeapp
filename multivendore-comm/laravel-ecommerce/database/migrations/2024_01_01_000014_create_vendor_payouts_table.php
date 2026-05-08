<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_payouts', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('vendor_id');
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('commission_deducted', 12, 2);
            $table->decimal('net_amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'processing', 'paid', 'failed'])->default('pending');
            $table->string('reference_id', 255)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('status');
            $table->foreign('vendor_id')->references('id')->on('vendors')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('vendor_payouts'); }
};

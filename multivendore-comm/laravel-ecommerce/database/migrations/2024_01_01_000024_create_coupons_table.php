<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('code', 50)->unique();
            $table->enum('type', ['percent', 'fixed', 'free_shipping'])->default('percent');
            $table->decimal('value', 10, 2);
            $table->decimal('min_order', 10, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->uuid('vendor_id')->nullable();   // null = platform-wide
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('vendor_id');
            $table->index('expires_at');

            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('coupons'); }
};

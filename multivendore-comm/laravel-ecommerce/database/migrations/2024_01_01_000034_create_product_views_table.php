<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_views', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('product_id');
            $table->uuid('user_id')->nullable();
            $table->string('session_id', 128)->nullable();
            $table->enum('referrer_type', ['search', 'category', 'recommendation', 'direct', 'ad', 'email'])->nullable();
            $table->timestamp('viewed_at')->useCurrent();
            $table->timestamps();

            $table->index('product_id');
            $table->index('user_id');
            $table->index('viewed_at');

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('product_views'); }
};

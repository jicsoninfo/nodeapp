<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('owner_user_id');
            $table->string('store_name', 200)->unique();
            $table->string('slug', 200)->unique();
            $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('pending');
            $table->enum('plan_type', ['free', 'basic', 'pro', 'enterprise'])->default('free');
            $table->decimal('commission_rate', 5, 2)->default(15.00);
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('owner_user_id');
            $table->index('status');
            $table->foreign('owner_user_id')->references('id')->on('users');
        });
    }

    public function down(): void { Schema::dropIfExists('vendors'); }
};

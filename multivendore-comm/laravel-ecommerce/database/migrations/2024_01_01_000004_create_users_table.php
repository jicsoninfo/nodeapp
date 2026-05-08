<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('email', 255)->unique();
            $table->string('phone', 30)->nullable()->unique();
            $table->string('password');
            $table->enum('status', ['active', 'pending', 'suspended', 'banned'])->default('pending');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void { Schema::dropIfExists('users'); }
};

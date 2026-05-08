<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auth_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id');
            $table->string('token_hash', 512)->unique();
            $table->enum('type', ['access', 'refresh', 'email_verify', 'password_reset', 'oauth']);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('auth_tokens'); }
};

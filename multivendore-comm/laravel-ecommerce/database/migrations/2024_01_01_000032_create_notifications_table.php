<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id');
            $table->string('type', 100);
            $table->enum('channel', ['email', 'sms', 'push', 'in_app'])->default('in_app');
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('user_notifications'); }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_device_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id');
            $table->string('fcm_token', 512);
            $table->string('device_type', 20)->default('unknown'); // ios | android | web
            $table->string('device_name', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'is_active']);
            $table->unique(['user_id', 'fcm_token']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('user_device_tokens'); }
};

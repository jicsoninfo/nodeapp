<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id');
            $table->string('label', 50)->default('Home');
            $table->string('full_name', 200);
            $table->string('line1', 255);
            $table->string('line2', 255)->nullable();
            $table->string('city', 100);
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20);
            $table->char('country_code', 2);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('addresses'); }
};

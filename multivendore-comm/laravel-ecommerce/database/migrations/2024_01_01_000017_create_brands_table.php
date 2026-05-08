<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('name', 200)->unique();
            $table->string('slug', 200)->unique();
            $table->string('logo_url', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('brands'); }
};

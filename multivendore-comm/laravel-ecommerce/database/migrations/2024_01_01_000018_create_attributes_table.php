<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('name', 100)->unique();
            $table->enum('type', ['text', 'number', 'boolean', 'color', 'size', 'select'])->default('text');
            $table->timestamps();
        });

        Schema::create('attribute_translations', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('attribute_id');
            $table->string('lang_code', 10);
            $table->string('label', 200);
            $table->timestamps();

            $table->unique(['attribute_id', 'lang_code']);
            $table->foreign('attribute_id')->references('id')->on('attributes')->cascadeOnDelete();
            $table->foreign('lang_code')->references('code')->on('languages');
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('attribute_id');
            $table->string('value', 255);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('attribute_id');
            $table->foreign('attribute_id')->references('id')->on('attributes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attribute_translations');
        Schema::dropIfExists('attributes');
    }
};

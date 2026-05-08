<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_translations', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('product_id');
            $table->string('lang_code', 10);
            $table->string('name', 500);
            $table->longText('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'lang_code']);
            $table->fullText(['name', 'short_description']);

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('lang_code')->references('code')->on('languages');
        });
    }

    public function down(): void { Schema::dropIfExists('product_translations'); }
};

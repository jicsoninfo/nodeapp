<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_translations', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('category_id');
            $table->string('lang_code', 10);
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['category_id', 'lang_code']);
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->foreign('lang_code')->references('code')->on('languages');
        });
    }

    public function down(): void { Schema::dropIfExists('category_translations'); }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('parent_id')->nullable();
            $table->string('slug', 200)->unique();
            $table->tinyInteger('depth')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('parent_id');
            $table->index('is_active');
            $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('categories'); }
};

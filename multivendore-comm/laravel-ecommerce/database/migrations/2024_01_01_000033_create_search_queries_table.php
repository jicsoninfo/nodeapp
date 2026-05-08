<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('search_queries', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id')->nullable();
            $table->string('session_id', 128)->nullable();
            $table->string('query', 500);
            $table->string('lang_code', 10)->default('en');
            $table->unsignedInteger('results_count')->default(0);
            $table->timestamp('searched_at')->useCurrent();
            $table->timestamps();

            $table->index('user_id');
            $table->index('query');
            $table->index('searched_at');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('search_queries'); }
};

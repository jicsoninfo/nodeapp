<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_policies', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('vendor_id');
            $table->enum('type', ['return', 'shipping', 'warranty']);
            $table->unsignedSmallInteger('return_window_days')->nullable();
            $table->text('condition')->nullable();
            $table->string('lang_code', 10)->default('en');
            $table->timestamps();

            $table->index('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('vendors')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('vendor_policies'); }
};

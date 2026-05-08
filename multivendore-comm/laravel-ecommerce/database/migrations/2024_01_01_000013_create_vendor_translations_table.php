<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_translations', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('vendor_id');
            $table->string('lang_code', 10);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'lang_code']);
            $table->foreign('vendor_id')->references('id')->on('vendors')->cascadeOnDelete();
            $table->foreign('lang_code')->references('code')->on('languages');
        });
    }

    public function down(): void { Schema::dropIfExists('vendor_translations'); }
};

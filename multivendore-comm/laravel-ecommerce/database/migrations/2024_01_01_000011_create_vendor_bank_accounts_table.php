<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('vendor_id');
            $table->string('account_holder', 200);
            $table->string('bank_name', 200);
            $table->text('account_number_enc');   // AES-256 encrypted
            $table->text('routing_number_enc')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('vendors')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('vendor_bank_accounts'); }
};

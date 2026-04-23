<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Currencies (separate from languages — one language can have many currencies)
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();    // ISO 4217: USD, EUR, SAR
            $table->string('name');                 // US Dollar
            $table->string('symbol', 10);           // $, €, ﷼
            $table->string('symbol_position')->default('before'); // before | after
            $table->integer('decimal_places')->default(2);
            $table->decimal('exchange_rate', 15, 6)->default(1.000000); // relative to base
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Price lists (e.g. Retail, Wholesale, VIP)
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // "Retail", "Wholesale"
            $table->string('code')->unique();      // "retail", "wholesale"
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Actual prices: one row per product × currency × price_list
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('price_list_id')->constrained()->cascadeOnDelete();
            $table->string('currency_code', 3);
            $table->decimal('amount', 15, 2);
            $table->decimal('sale_amount', 15, 2)->nullable();
            $table->timestamp('sale_starts_at')->nullable();
            $table->timestamp('sale_ends_at')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'price_list_id', 'currency_code']);
            $table->foreign('currency_code')->references('code')->on('currencies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('price_lists');
        Schema::dropIfExists('currencies');
    }
};

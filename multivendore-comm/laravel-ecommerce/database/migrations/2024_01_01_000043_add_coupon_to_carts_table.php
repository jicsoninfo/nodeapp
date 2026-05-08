<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Only add if not already present (idempotent)
        if (!Schema::hasColumn('carts', 'coupon_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->uuid('coupon_id')->nullable()->after('currency');
                $table->foreign('coupon_id')->references('id')->on('coupons')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('carts', 'coupon_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropForeign(['coupon_id']);
                $table->dropColumn('coupon_id');
            });
        }
    }
};

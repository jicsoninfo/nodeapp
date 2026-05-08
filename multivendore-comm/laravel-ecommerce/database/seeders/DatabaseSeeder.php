<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Order matters — respect foreign key dependencies.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            // 1. Reference / lookup data
            LanguageSeeder::class,
            CurrencySeeder::class,
            ExchangeRateSeeder::class,

            // 2. Roles & permissions
            RolePermissionSeeder::class,

            // 3. Users (admin, vendors, buyers)
            UserSeeder::class,

            // 4. Vendor data
            VendorSeeder::class,

            // 5. Catalog
            CategorySeeder::class,
            BrandSeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,

            // 6. Commerce
            CouponSeeder::class,
            OrderSeeder::class,

            // 7. UGC & analytics
            ReviewSeeder::class,
            WishlistSeeder::class,
            SearchQuerySeeder::class,

            // 8. Platform settings
            SettingSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Database seeding complete!');
    }
}

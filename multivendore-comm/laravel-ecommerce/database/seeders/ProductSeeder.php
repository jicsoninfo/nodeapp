<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\ProductTranslation;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Use factory for 30 random products per vendor
        $vendors = Vendor::all();
        foreach ($vendors as $vendor) {
            Product::factory(30)->create(['vendor_id' => $vendor->id]);
        }

        $this->command->info('  Products seeded (' . Product::count() . ' total)');
    }
}

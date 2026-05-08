<?php
namespace Tests\Feature\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductVariant;
use App\Models\Vendor;
use App\Models\VendorProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_browse_active_products(): void
    {
        $vendor  = Vendor::factory()->create(['status' => 'active']);
        VendorProfile::factory()->create(['vendor_id' => $vendor->id]);
        $product = Product::factory()->create(['vendor_id' => $vendor->id, 'status' => 'active']);
        ProductTranslation::factory()->create(['product_id' => $product->id, 'lang_code' => 'en']);

        $this->getJson('/api/v1/public/products')
             ->assertStatus(200)
             ->assertJsonStructure(['data']);
    }

    public function test_draft_product_not_visible_publicly(): void
    {
        $vendor  = Vendor::factory()->create(['status' => 'active']);
        $product = Product::factory()->create(['vendor_id' => $vendor->id, 'status' => 'draft']);

        $this->getJson("/api/v1/public/products/{$product->id}")->assertStatus(404);
    }

    public function test_vendor_can_create_product(): void
    {
        [$user, $vendor] = $this->actingAsVendor();
        $category        = Category::factory()->create();
        $brand           = Brand::factory()->create();

        $response = $this->postJson('/api/v1/vendor/products', [
            'product'      => ['category_id' => $category->id, 'brand_id' => $brand->id, 'status' => 'draft'],
            'translations' => [['lang_code' => 'en', 'name' => 'Test Product', 'description' => 'A test product']],
            'variants'     => [['data' => ['sku' => 'TEST-001', 'price' => 49.99, 'currency' => 'USD', 'stock_quantity' => 10], 'attribute_values' => []]],
        ]);

        $response->assertStatus(201)->assertJsonPath('data.asin', fn ($v) => ! empty($v));
        $this->assertDatabaseHas('products', ['vendor_id' => $vendor->id]);
        $this->assertDatabaseHas('product_translations', ['lang_code' => 'en', 'name' => 'Test Product']);
        $this->assertDatabaseHas('product_variants', ['sku' => 'TEST-001']);
    }

    public function test_vendor_cannot_edit_another_vendors_product(): void
    {
        $this->actingAsVendor();
        $otherVendor = Vendor::factory()->create(['status' => 'active']);
        $product     = Product::factory()->create(['vendor_id' => $otherVendor->id]);

        $this->putJson("/api/v1/vendor/products/{$product->id}", ['product' => ['status' => 'active']])
             ->assertStatus(403);
    }

    public function test_admin_can_change_product_status(): void
    {
        $this->actingAsAdmin();
        $vendor  = Vendor::factory()->create(['status' => 'active']);
        $product = Product::factory()->create(['vendor_id' => $vendor->id, 'status' => 'draft']);

        $this->patchJson("/api/v1/admin/products/{$product->id}/status", ['status' => 'active'])
             ->assertStatus(200);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'status' => 'active']);
    }
}

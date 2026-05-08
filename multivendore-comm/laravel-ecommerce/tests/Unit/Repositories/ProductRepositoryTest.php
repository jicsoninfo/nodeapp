<?php
namespace Tests\Unit\Repositories;
use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\VendorProfile;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new ProductRepository(new Product());
    }

    public function test_find_by_asin_returns_correct_product(): void
    {
        $vendor  = Vendor::factory()->create(['status' => 'active']);
        VendorProfile::factory()->create(['vendor_id' => $vendor->id]);
        $product = Product::factory()->create(['vendor_id' => $vendor->id, 'asin' => 'TESTB0001']);

        $found = $this->repo->findByAsin('TESTB0001');

        $this->assertNotNull($found);
        $this->assertEquals($product->id, $found->id);
    }

    public function test_find_by_asin_returns_null_for_missing(): void
    {
        $this->assertNull($this->repo->findByAsin('NOTEXIST'));
    }

    public function test_update_rating_recalculates_correctly(): void
    {
        $vendor  = Vendor::factory()->create(['status' => 'active']);
        $product = Product::factory()->create(['vendor_id' => $vendor->id]);
        $user1   = \App\Models\User::factory()->create();
        $user2   = \App\Models\User::factory()->create();

        \App\Models\Review::factory()->create(['product_id' => $product->id, 'user_id' => $user1->id, 'rating' => 5, 'status' => 'approved']);
        \App\Models\Review::factory()->create(['product_id' => $product->id, 'user_id' => $user2->id, 'rating' => 3, 'status' => 'approved']);

        $this->repo->updateRating($product->id);

        $product->refresh();
        $this->assertEquals(4.00, (float) $product->avg_rating);
        $this->assertEquals(2, $product->total_reviews);
    }

    public function test_get_featured_returns_highest_rated_active_products(): void
    {
        $vendor  = Vendor::factory()->create(['status' => 'active']);

        Product::factory()->create(['vendor_id' => $vendor->id, 'status' => 'active',   'avg_rating' => 4.9]);
        Product::factory()->create(['vendor_id' => $vendor->id, 'status' => 'inactive', 'avg_rating' => 5.0]);
        Product::factory()->create(['vendor_id' => $vendor->id, 'status' => 'active',   'avg_rating' => 4.2]);

        $featured = $this->repo->getFeatured(5);

        $this->assertCount(2, $featured);
        $this->assertEquals(4.9, (float) $featured->first()->avg_rating);
    }
}

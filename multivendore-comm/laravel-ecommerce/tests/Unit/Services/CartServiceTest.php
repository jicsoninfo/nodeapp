<?php
namespace Tests\Unit\Services;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidCouponException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();
    }

    public function test_creates_cart_for_user(): void
    {
        $user = User::factory()->create();
        $cart = $this->cartService->getOrCreate($user);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals($user->id, $cart->user_id);
    }

    public function test_creates_guest_cart_by_session(): void
    {
        $cart = $this->cartService->getOrCreate('session_abc123');

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals('session_abc123', $cart->session_id);
        $this->assertNull($cart->user_id);
    }

    public function test_adds_item_to_cart(): void
    {
        $user    = User::factory()->create();
        $vendor  = Vendor::factory()->create(['status' => 'active']);
        $variant = ProductVariant::factory()->create([
            'stock_quantity' => 10,
            'is_active'      => true,
            'price'          => 29.99,
        ]);

        // Make product belong to vendor
        $variant->product()->update(['vendor_id' => $vendor->id]);

        $cart = $this->cartService->getOrCreate($user);
        $cart = $this->cartService->addItem($cart, $variant->id, 2);

        $this->assertEquals(1, $cart->items->count());
        $this->assertEquals(2, $cart->items->first()->quantity);
    }

    public function test_cannot_add_item_exceeding_stock(): void
    {
        $this->expectException(InsufficientStockException::class);

        $user    = User::factory()->create();
        $variant = ProductVariant::factory()->create(['stock_quantity' => 3, 'is_active' => true]);
        $cart    = $this->cartService->getOrCreate($user);

        $this->cartService->addItem($cart, $variant->id, 10);
    }

    public function test_applies_valid_coupon(): void
    {
        $user   = User::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'SAVE10', 'type' => 'percent', 'value' => 10, 'min_order' => 0]);
        $cart   = Cart::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $cart = $this->cartService->applyCoupon($cart, 'SAVE10');

        $this->assertEquals($coupon->id, $cart->coupon_id);
    }

    public function test_rejects_expired_coupon(): void
    {
        $this->expectException(InvalidCouponException::class);

        $user   = User::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'EXPIRED', 'expires_at' => now()->subDay()]);
        $cart   = Cart::factory()->create(['user_id' => $user->id]);

        $this->cartService->applyCoupon($cart, 'EXPIRED');
    }

    public function test_clears_all_cart_items(): void
    {
        $user    = User::factory()->create();
        $cart    = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory(3)->create(['cart_id' => $cart->id]);

        $this->cartService->clear($cart);

        $this->assertEquals(0, $cart->items()->count());
    }
}

<?php
namespace Tests\Feature\Order;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Vendor;
use App\Models\VendorProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_view_their_orders(): void
    {
        $user = $this->actingAsUser('buyer');
        Order::factory(3)->create(['user_id' => $user->id]);

        $this->getJson('/api/v1/buyer/orders')
             ->assertStatus(200)
             ->assertJsonCount(3, 'data.data');
    }

    public function test_buyer_cannot_view_other_users_order(): void
    {
        $this->actingAsUser('buyer');
        $otherOrder = Order::factory()->create();

        $this->getJson("/api/v1/buyer/orders/{$otherOrder->id}")->assertStatus(403);
    }

    public function test_buyer_can_cancel_pending_order(): void
    {
        $user    = $this->actingAsUser('buyer');
        $address = Address::factory()->create(['user_id' => $user->id]);
        $order   = Order::factory()->create(['user_id' => $user->id, 'address_id' => $address->id, 'status' => 'pending']);

        $this->postJson("/api/v1/buyer/orders/{$order->id}/cancel", ['reason' => 'Changed my mind'])
             ->assertStatus(200);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
    }

    public function test_buyer_cannot_cancel_delivered_order(): void
    {
        $user    = $this->actingAsUser('buyer');
        $address = Address::factory()->create(['user_id' => $user->id]);
        $order   = Order::factory()->create(['user_id' => $user->id, 'address_id' => $address->id, 'status' => 'delivered']);

        $this->postJson("/api/v1/buyer/orders/{$order->id}/cancel")
             ->assertStatus(422);
    }

    public function test_order_status_transitions_are_validated(): void
    {
        $order = Order::factory()->create(['status' => 'delivered']);

        $this->expectException(\LogicException::class);
        $order->transitionTo(\App\Enums\OrderStatus::Pending);
    }

    public function test_admin_can_update_order_status(): void
    {
        $this->actingAsAdmin();
        $order = Order::factory()->create(['status' => 'pending']);

        $this->patchJson("/api/v1/admin/orders/{$order->id}/status", ['status' => 'confirmed'])
             ->assertStatus(200);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'confirmed']);
    }
}

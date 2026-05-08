<?php
namespace Tests\Unit\Services;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_number_is_unique_format(): void
    {
        $number = Order::generateOrderNumber();
        $this->assertMatchesRegularExpression('/^ORD-\d{4}-\d{6}$/', $number);
    }

    public function test_pending_order_can_transition_to_confirmed(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);
        $order->transitionTo(OrderStatus::Confirmed);
        $this->assertEquals(OrderStatus::Confirmed, $order->fresh()->status);
    }

    public function test_delivered_order_cannot_transition_to_pending(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::Delivered]);
        $this->expectException(\LogicException::class);
        $order->transitionTo(OrderStatus::Pending);
    }

    public function test_pending_and_confirmed_orders_can_be_cancelled(): void
    {
        $this->assertTrue(OrderStatus::Pending->canCancel());
        $this->assertTrue(OrderStatus::Confirmed->canCancel());
        $this->assertFalse(OrderStatus::Delivered->canCancel());
        $this->assertFalse(OrderStatus::Shipped->canCancel());
    }

    public function test_final_statuses_are_correct(): void
    {
        $this->assertTrue(OrderStatus::Delivered->isFinal());
        $this->assertTrue(OrderStatus::Cancelled->isFinal());
        $this->assertTrue(OrderStatus::Refunded->isFinal());
        $this->assertFalse(OrderStatus::Pending->isFinal());
        $this->assertFalse(OrderStatus::Processing->isFinal());
    }
}

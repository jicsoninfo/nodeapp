<?php
namespace Tests\Unit\Services;
use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    private CurrencyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CurrencyService();
    }

    public function test_same_currency_returns_same_amount(): void
    {
        $result = $this->service->convert(100.00, 'USD', 'USD');
        $this->assertEquals(100.00, $result);
    }

    public function test_converts_using_exchange_rate(): void
    {
        ExchangeRate::factory()->create(['from_currency' => 'USD', 'to_currency' => 'INR', 'rate' => 83.52]);

        $result = $this->service->convert(10.00, 'USD', 'INR');
        $this->assertEquals(835.20, $result);
    }

    public function test_formats_usd_amount(): void
    {
        $formatted = $this->service->formatAmount(29.99, 'USD');
        $this->assertEquals('$29.99', $formatted);
    }

    public function test_formats_inr_amount(): void
    {
        $formatted = $this->service->formatAmount(999.00, 'INR');
        $this->assertEquals('₹999.00', $formatted);
    }
}

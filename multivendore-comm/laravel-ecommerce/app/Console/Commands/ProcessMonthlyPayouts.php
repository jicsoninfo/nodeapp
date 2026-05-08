<?php
namespace App\Console\Commands;
use App\Jobs\ProcessVendorPayout;
use App\Models\Vendor;
use App\Models\VendorPayout;
use App\Services\VendorPayoutService;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Throwable;

class ProcessMonthlyPayouts extends Command
{
    protected $signature   = 'payouts:process-monthly {--dry-run : Show what would be paid without actually processing}';
    protected $description = 'Calculate and dispatch monthly vendor payouts';

    public function __construct(private readonly VendorPayoutService $payoutService) { parent::__construct(); }

    public function handle(): int
    {
        $from = now()->startOfMonth()->toDateTimeString();
        $to   = now()->endOfMonth()->toDateTimeString();

        $this->info("Processing payouts for {$from} → {$to}");

        $vendors = Vendor::active()->with('primaryBankAccount')->get();
        $jobs    = [];
        $total   = 0;

        foreach ($vendors as $vendor) {
            $gross = $this->payoutService->calculatePendingPayout($vendor, $from, $to);

            if ($gross < config('marketplace.min_payout_amount', 50)) {
                $this->line("  SKIP {$vendor->store_name} — gross {$gross} below minimum");
                continue;
            }

            $this->line("  {$vendor->store_name}: gross={$gross}");
            $total += $gross;

            if (! $this->option('dry-run')) {
                $payout = $this->payoutService->createPayout($vendor, $gross);
                $jobs[] = new ProcessVendorPayout($payout);
            }
        }

        $this->info("Total gross: {$total}");

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN — no payouts dispatched.');
            return self::SUCCESS;
        }

        if (empty($jobs)) {
            $this->info('No payouts to process.');
            return self::SUCCESS;
        }

        Bus::batch($jobs)
            ->name('Monthly Vendor Payouts — ' . now()->format('Y-m'))
            ->allowFailures()
            ->catch(fn (Batch $batch, Throwable $e) => \Log::error('Payout batch failed', ['error' => $e->getMessage()]))
            ->dispatch();

        $this->info(count($jobs) . ' payout jobs dispatched to queue.');
        return self::SUCCESS;
    }
}

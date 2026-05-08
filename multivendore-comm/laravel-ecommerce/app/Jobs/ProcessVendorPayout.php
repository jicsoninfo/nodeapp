<?php

namespace App\Jobs;

use App\Enums\PayoutStatus;
use App\Models\Vendor;
use App\Models\VendorPayout;
use App\Notifications\Vendor\PayoutProcessedNotification;
use App\Notifications\Vendor\PayoutFailedNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

/**
 * Processes a single vendor payout via Stripe Connect (or bank transfer).
 * Designed to run in a Bus::batch() for monthly batch processing.
 *
 * Dispatch:
 *   ProcessVendorPayout::dispatch($payout);
 *
 * Batch dispatch (from admin):
 *   Bus::batch(
 *       VendorPayout::where('status', 'pending')->get()
 *           ->map(fn ($p) => new ProcessVendorPayout($p))
 *   )->name('Monthly Payouts')->dispatch();
 */
class ProcessVendorPayout implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int    $tries           = 2;
    public int    $timeout         = 120;
    public string $queue           = 'payouts';
    public int    $backoff         = 300; // 5 min between retries

    public function __construct(public readonly VendorPayout $payout) {}

    public function handle(): void
    {
        // Skip if cancelled via batch
        if ($this->batch()?->cancelled()) return;

        // Guard: only process pending payouts
        if ($this->payout->status !== PayoutStatus::Pending) {
            Log::info('ProcessVendorPayout skipped — not pending', ['payout_id' => $this->payout->id]);
            return;
        }

        DB::transaction(function () {
            // Lock the row for update to prevent double processing
            $payout = VendorPayout::lockForUpdate()->find($this->payout->id);

            if (! $payout || $payout->status !== PayoutStatus::Pending) return;

            $payout->update(['status' => PayoutStatus::Processing]);

            try {
                $referenceId = $this->sendPayout($payout);

                $payout->update([
                    'status'       => PayoutStatus::Paid,
                    'reference_id' => $referenceId,
                    'paid_at'      => now(),
                ]);

                // Notify vendor owner
                $payout->vendor->owner->notify(
                    new PayoutProcessedNotification($payout)
                );

                Log::info('Vendor payout completed', [
                    'payout_id'    => $payout->id,
                    'vendor_id'    => $payout->vendor_id,
                    'net_amount'   => $payout->net_amount,
                    'reference_id' => $referenceId,
                ]);

            } catch (\Throwable $e) {
                $payout->update(['status' => PayoutStatus::Failed]);

                $payout->vendor->owner->notify(
                    new PayoutFailedNotification($payout, $e->getMessage())
                );

                Log::error('Vendor payout failed', [
                    'payout_id' => $payout->id,
                    'error'     => $e->getMessage(),
                ]);

                throw $e; // Re-throw so the queue marks the job as failed
            }
        });
    }

    /**
     * Send the actual payout via Stripe Connect or bank transfer API.
     * Returns a reference/transaction ID string.
     */
    private function sendPayout(VendorPayout $payout): string
    {
        $vendor      = $payout->vendor->load('primaryBankAccount');
        $bankAccount = $vendor->primaryBankAccount;

        // Stripe Connect transfer
        if (config('services.stripe.secret')) {
            $stripe   = new StripeClient(config('services.stripe.secret'));
            $transfer = $stripe->transfers->create([
                'amount'      => (int) ($payout->net_amount * 100),
                'currency'    => strtolower($payout->currency),
                'destination' => $bankAccount?->stripe_account_id ?? config('services.stripe.platform_account'),
                'metadata'    => [
                    'payout_id' => $payout->id,
                    'vendor_id' => $payout->vendor_id,
                ],
            ]);
            return $transfer->id;
        }

        // Fallback: manual reference number (for COD / wire transfer platforms)
        return 'MANUAL-PAYOUT-' . strtoupper(substr($payout->id, 0, 8)) . '-' . now()->format('Ymd');
    }

    public function failed(\Throwable $e): void
    {
        // Final failure after all retries exhausted — mark as failed
        $this->payout->updateQuietly(['status' => PayoutStatus::Failed]);

        Log::error('ProcessVendorPayout exhausted retries', [
            'payout_id' => $this->payout->id,
            'error'     => $e->getMessage(),
        ]);
    }
}

<?php

namespace App\Services;

use App\Enums\PayoutStatus;
use App\Models\Vendor;
use App\Models\VendorPayout;
use Illuminate\Support\Facades\DB;

class VendorPayoutService
{
    public function calculatePendingPayout(Vendor $vendor, string $from, string $to): float
    {
        return (float) DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.vendor_id', $vendor->id)
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.placed_at', [$from, $to])
            ->whereNotExists(fn ($q) =>
                $q->from('vendor_payouts')
                  ->whereColumn('vendor_payouts.vendor_id', 'order_items.vendor_id')
                  ->where('vendor_payouts.status', PayoutStatus::Paid->value)
                  ->whereRaw('vendor_payouts.created_at BETWEEN ? AND ?', [$from, $to])
            )
            ->sum(DB::raw('order_items.unit_price * order_items.quantity'));
    }

    public function createPayout(Vendor $vendor, float $grossAmount): VendorPayout
    {
        $commissionRate    = $vendor->effectiveCommissionRate();
        $commissionAmount  = round($grossAmount * ($commissionRate / 100), 2);
        $netAmount         = $grossAmount - $commissionAmount;

        return VendorPayout::create([
            'vendor_id'           => $vendor->id,
            'gross_amount'        => $grossAmount,
            'commission_deducted' => $commissionAmount,
            'net_amount'          => $netAmount,
            'currency'            => 'USD',
            'status'              => PayoutStatus::Pending,
        ]);
    }

    public function processPayout(VendorPayout $payout): void
    {
        $payout->update(['status' => PayoutStatus::Processing]);

        // TODO: Integrate with Stripe Connect / bank transfer API
        // On success:
        $payout->update([
            'status'       => PayoutStatus::Paid,
            'reference_id' => 'PAYOUT-REF-' . uniqid(),
            'paid_at'      => now(),
        ]);
    }
}

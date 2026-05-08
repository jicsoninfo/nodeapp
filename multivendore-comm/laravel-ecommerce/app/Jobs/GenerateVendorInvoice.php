<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateVendorInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue   = 'default';
    public int    $timeout = 60;

    public function __construct(
        public readonly Order  $order,
        public readonly Vendor $vendor,
    ) {}

    public function handle(): void
    {
        $items = $this->order->items()
            ->where('vendor_id', $this->vendor->id)
            ->with('variant.product.translations')
            ->get();

        $pdf  = Pdf::loadView('pdfs.vendor-invoice', [
            'order'  => $this->order,
            'vendor' => $this->vendor,
            'items'  => $items,
        ]);

        $path = "invoices/vendors/{$this->vendor->id}/{$this->order->order_number}.pdf";
        Storage::disk('s3')->put($path, $pdf->output());
    }
}

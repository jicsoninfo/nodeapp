<?php
namespace App\Events\Vendor;
use App\Models\Vendor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorApplicationReceived
{
    use Dispatchable, SerializesModels;
    public function __construct(public readonly Vendor $vendor) {}
}

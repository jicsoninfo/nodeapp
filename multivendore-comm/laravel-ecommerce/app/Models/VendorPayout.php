<?php
namespace App\Models;
use App\Enums\PayoutStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPayout extends Model
{
    use HasUuid;
    protected $fillable = ['vendor_id','gross_amount','commission_deducted','net_amount','currency','status','reference_id','paid_at'];
    protected $casts    = ['status' => PayoutStatus::class, 'gross_amount' => 'decimal:2', 'commission_deducted' => 'decimal:2', 'net_amount' => 'decimal:2', 'paid_at' => 'datetime'];
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function scopePending($q) { return $q->where('status', PayoutStatus::Pending); }
    public function scopePaid($q)    { return $q->where('status', PayoutStatus::Paid); }
}

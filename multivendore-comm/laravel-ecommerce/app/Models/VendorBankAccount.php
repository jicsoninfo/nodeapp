<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorBankAccount extends Model
{
    use HasUuid;
    protected $fillable = ['vendor_id','account_holder','bank_name','account_number_enc','routing_number_enc','is_primary'];
    protected $hidden   = ['account_number_enc','routing_number_enc'];
    protected $casts    = ['is_primary' => 'boolean'];
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
}

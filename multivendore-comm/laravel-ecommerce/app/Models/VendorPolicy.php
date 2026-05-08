<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPolicy extends Model
{
    use HasUuid;
    protected $fillable = ['vendor_id','type','return_window_days','condition','lang_code'];
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
}

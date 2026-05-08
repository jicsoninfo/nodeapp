<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorTranslation extends Model
{
    use HasUuid;
    protected $fillable = ['vendor_id','lang_code','description'];
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
}

<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorProfile extends Model
{
    use HasUuid;

    protected $fillable = [
        'vendor_id', 'description', 'logo_url', 'banner_url',
        'business_type', 'tax_id', 'avg_rating', 'total_reviews', 'website_url',
    ];

    protected $casts = [
        'avg_rating'    => 'decimal:2',
        'total_reviews' => 'integer',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

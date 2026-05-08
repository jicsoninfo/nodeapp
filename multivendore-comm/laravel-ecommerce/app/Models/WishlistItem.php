<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    use HasUuid;
    protected $fillable = ['wishlist_id','variant_id','added_at'];
    protected $casts    = ['added_at' => 'datetime'];
    public function wishlist(): BelongsTo { return $this->belongsTo(Wishlist::class); }
    public function variant(): BelongsTo  { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
}

<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    use HasUuid;
    protected $fillable = ['product_id','variant_id','url','type','alt_text','sort_order'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
}

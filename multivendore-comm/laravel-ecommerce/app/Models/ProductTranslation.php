<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslation extends Model
{
    use HasUuid;
    protected $fillable = ['product_id','lang_code','name','description','short_description','meta_title','meta_description'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}

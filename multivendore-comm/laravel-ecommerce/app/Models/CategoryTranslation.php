<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTranslation extends Model
{
    use HasUuid;
    protected $fillable = ['category_id','lang_code','name','description'];
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
}

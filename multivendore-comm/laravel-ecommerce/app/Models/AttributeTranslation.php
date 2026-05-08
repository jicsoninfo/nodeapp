<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeTranslation extends Model
{
    use HasUuid;
    protected $fillable = ['attribute_id', 'lang_code', 'label'];
    public function attribute(): BelongsTo { return $this->belongsTo(Attribute::class); }
}

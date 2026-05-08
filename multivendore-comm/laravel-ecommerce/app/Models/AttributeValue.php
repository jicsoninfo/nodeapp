<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    use HasUuid;
    protected $fillable = ['attribute_id', 'value', 'sort_order'];
    public function attribute(): BelongsTo { return $this->belongsTo(Attribute::class); }
}

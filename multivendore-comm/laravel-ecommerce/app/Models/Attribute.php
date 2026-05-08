<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasUuid;
    protected $fillable = ['name', 'type'];
    public function values(): HasMany       { return $this->hasMany(AttributeValue::class); }
    public function translations(): HasMany { return $this->hasMany(AttributeTranslation::class); }
}

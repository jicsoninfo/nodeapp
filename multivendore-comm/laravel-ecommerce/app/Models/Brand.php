<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Brand extends Model
{
    use HasUuid, HasSlug;
    protected $fillable = ['name', 'slug', 'logo_url', 'is_verified'];
    protected $casts    = ['is_verified' => 'boolean'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function scopeVerified($q) { return $q->where('is_verified', true); }
}

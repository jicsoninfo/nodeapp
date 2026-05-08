<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasUuid, HasSlug;

    protected $fillable = ['parent_id', 'slug', 'depth', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
        'depth'      => 'integer',
        'sort_order' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn ($model) => $model->translations->first()?->name ?? 'category')
            ->saveSlugsTo('slug');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getTranslation(string $locale, string $fallback = 'en'): ?CategoryTranslation
    {
        return $this->translations->firstWhere('lang_code', $locale)
            ?? $this->translations->firstWhere('lang_code', $fallback);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}

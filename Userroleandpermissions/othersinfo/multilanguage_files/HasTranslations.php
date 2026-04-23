<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Attach to any Model that has a `{model}_translations` table.
 *
 * Expects the host model to define:
 *   protected string $translationModel  = SomeTranslation::class;
 *   protected string $translationFk     = 'some_id';          // FK on translations table
 *
 * Each translation model must have: language_code, + whatever translated fields.
 */
trait HasTranslations
{
    // ── Core relationship ────────────────────────────────────────────────────

    public function translations(): HasMany
    {
        return $this->hasMany($this->translationModel, $this->translationFk);
    }

    // ── Translation resolution with fallback ─────────────────────────────────

    /**
     * Get a translation for the given locale.
     * Falls back to the default language (usually 'en') if not found.
     */
    public function translation(string $locale = null): ?object
    {
        $locale   = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        // Try the requested locale first
        $translation = $this->translations
            ->firstWhere('language_code', $locale);

        // Fall back to default language
        if (! $translation && $locale !== $fallback) {
            $translation = $this->translations
                ->firstWhere('language_code', $fallback);
        }

        return $translation;
    }

    /**
     * Shorthand: get a specific translated field.
     *
     * Usage: $product->trans('name')
     *        $product->trans('name', 'ar')
     */
    public function trans(string $field, string $locale = null): ?string
    {
        return $this->translation($locale)?->{$field} ?? null;
    }

    // ── Magic attribute accessor ─────────────────────────────────────────────

    /**
     * Allow $product->name  →  $product->trans('name')  automatically.
     *
     * List translatable fields on the host model:
     *   protected array $translatable = ['name', 'description'];
     */
    public function __get($key)
    {
        if (
            isset($this->translatable)
            && in_array($key, $this->translatable, true)
        ) {
            return $this->trans($key);
        }

        return parent::__get($key);
    }

    // ── Eager-load helper ────────────────────────────────────────────────────

    /**
     * Scope: load only translations for the current locale + fallback.
     * Reduces query size vs loading all translations.
     *
     * Usage: Product::withActiveTranslations()->get()
     */
    public function scopeWithActiveTranslations($query, string $locale = null): void
    {
        $locale   = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        $locales = array_unique([$locale, $fallback]);

        $query->with(['translations' => fn ($q) => $q->whereIn('language_code', $locales)]);
    }

    // ── Upsert translation ───────────────────────────────────────────────────

    /**
     * Create or update a translation for a given locale.
     *
     * Usage: $product->setTranslation('ar', ['name' => 'منتج', 'description' => '...'])
     */
    public function setTranslation(string $locale, array $data): object
    {
        return $this->translations()->updateOrCreate(
            ['language_code' => $locale],
            $data
        );
    }
}

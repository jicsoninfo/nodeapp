<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key','value','type','group','description'];

    /** Get a setting value with optional default, auto-cast by type. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (! $setting) return $default;
            return match($setting->type) {
                'boolean' => (bool) $setting->value,
                'integer' => (int)  $setting->value,
                'json'    => json_decode($setting->value, true),
                default   => $setting->value,
            };
        });
    }

    /** Update a setting and bust the cache. */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting:{$key}");
    }
}

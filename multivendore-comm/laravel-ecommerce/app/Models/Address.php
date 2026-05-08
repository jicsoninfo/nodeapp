<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id', 'label', 'full_name', 'line1', 'line2',
        'city', 'state', 'postal_code', 'country_code', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAttribute(): string
    {
        return collect([$this->line1, $this->line2, $this->city, $this->state, $this->postal_code, $this->country_code])
            ->filter()
            ->implode(', ');
    }
}

<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wishlist extends Model
{
    use HasUuid;
    protected $fillable = ['user_id','name','is_public'];
    protected $casts    = ['is_public' => 'boolean'];
    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function items(): HasMany    { return $this->hasMany(WishlistItem::class); }
}

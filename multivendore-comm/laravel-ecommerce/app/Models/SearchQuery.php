<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchQuery extends Model
{
    use HasUuid;
    protected $fillable = ['user_id','session_id','query','lang_code','results_count','searched_at'];
    protected $casts    = ['searched_at' => 'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

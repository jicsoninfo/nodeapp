<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewMedia extends Model
{
    use HasUuid;
    protected $fillable = ['review_id','url','type'];
    public function review(): BelongsTo { return $this->belongsTo(Review::class); }
}

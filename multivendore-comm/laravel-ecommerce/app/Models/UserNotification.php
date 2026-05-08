<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    use HasUuid;
    protected $fillable = ['user_id','type','channel','title','body','data','is_read','read_at'];
    protected $casts    = ['data' => 'array', 'is_read' => 'boolean', 'read_at' => 'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function markRead(): void  { $this->update(['is_read' => true, 'read_at' => now()]); }
    public function scopeUnread($q)  { return $q->where('is_read', false); }
}

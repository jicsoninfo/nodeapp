<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasUuid;
    protected $fillable = ['order_item_id','carrier','tracking_number','status','shipped_at','estimated_at','delivered_at'];
    protected $casts    = ['shipped_at' => 'datetime', 'estimated_at' => 'datetime', 'delivered_at' => 'datetime'];
    public function orderItem(): BelongsTo { return $this->belongsTo(OrderItem::class); }
    public function isDelivered(): bool    { return $this->status === 'delivered'; }
}

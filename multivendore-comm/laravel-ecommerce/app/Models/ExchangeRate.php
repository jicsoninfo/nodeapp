<?php
namespace App\Models;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasUuid;
    protected $fillable = ['from_currency','to_currency','rate','fetched_at'];
    protected $casts    = ['rate' => 'decimal:8', 'fetched_at' => 'datetime'];
}

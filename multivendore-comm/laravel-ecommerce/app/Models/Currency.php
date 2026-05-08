<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $primaryKey = 'code';
    protected $keyType    = 'string';
    public    $incrementing = false;
    protected $fillable   = ['code','name','symbol','decimal_places','is_active'];
    protected $casts      = ['is_active' => 'boolean', 'decimal_places' => 'integer'];
    public function scopeActive($q) { return $q->where('is_active', true); }
}

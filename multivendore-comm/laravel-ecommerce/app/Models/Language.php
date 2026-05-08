<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $primaryKey = 'code';
    protected $keyType    = 'string';
    public    $incrementing = false;
    protected $fillable   = ['code','name','native_name','direction','is_active','is_default'];
    protected $casts      = ['is_active' => 'boolean', 'is_default' => 'boolean'];
    public function scopeActive($q) { return $q->where('is_active', true); }
}

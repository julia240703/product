<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code', // mis: FIF/09/SEP/25/JATA123
    ];

    // ================
    // Relations
    // ================

    public function creditHeaders()
    {
        return $this->hasMany(CreditHeader::class, 'credit_provider_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApparelImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'apparel_id',
        'image',
        'caption',
        'sort',
    ];

    public function apparel()
    {
        return $this->belongsTo(Apparel::class);
    }
}
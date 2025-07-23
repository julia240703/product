<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apparel extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(ApparelCategory::class);
    }

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'size',
        'color',
        'material',
        'image_url',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apparel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_apparel',
        'image',
        'category_id',
        'description',
        'dimensions',
        'weight',
        'color',
        'size',
        'part_number',
    ];

    /**
    * Relationship to ApparelCategory
    * An apparel belongs to one category
    */
    public function category()
    {
        return $this->belongsTo(ApparelCategory::class, 'category_id');
    }
}
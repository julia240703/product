<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apparel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_apparel',
        'cover_image', 
        'category_id',
        'description',
        'material',
        'dimensions',
        'weight',
        'color',
        'size',
        'part_number',
        'stock',
        'is_new',
    ];

    protected $casts = [
        'is_new' => 'boolean', 
    ];

    /**
    * Relationship to ApparelCategory
    * An apparel belongs to one category
    */
    public function category()
    {
        return $this->belongsTo(ApparelCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ApparelImage::class)->orderBy('sort');
    }
}
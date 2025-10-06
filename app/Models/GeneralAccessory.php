<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralAccessory extends Model
{
    use HasFactory;

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants() {
        return $this->hasMany(GeneralAccessoryVariant::class);
    }

    public function images() {
        return $this->hasMany(GeneralAccessoryImage::class)->orderBy('sort');
    }

    protected $fillable = [
        'name',
        'cover_image',
        'part_number',
        'dimension',
        'weight',
        'price',
        'description',
        'variant',
        'material',
        'color',
        'stock',
        'category_id',
    ];
}

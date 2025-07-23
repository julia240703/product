<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorAccessory extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(AccessoryCategory::class, 'category_id');
    }

    protected $fillable = [
        'category_id',
        'name',
        'function',
        'color',
        'material',
        'part_number',
        'price',
        'image_url',
        'description',
    ];
}

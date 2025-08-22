<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorAccessory extends Model
{
    use HasFactory;

    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    protected $fillable = [
        'name',
        'image',
        'part_number',
        'dimension',
        'weight',
        'motor_id',
        'price',
        'description',
        'material',
        'category_id',
    ];
}

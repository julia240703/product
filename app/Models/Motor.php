<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'color',
    ];

    public function category()
    {
        return $this->belongsTo(MotorCategory::class, 'category_id');
    }

    public function features()
    {
        return $this->hasMany(MotorFeature::class);
    }

    public function colors()
    {
        return $this->hasMany(MotorColor::class);
    }

    public function specs()
    {
        return $this->hasMany(MotorSpecification::class);
    }

    public function parts()
    {
        return $this->hasMany(MotorPart::class);
    }
}

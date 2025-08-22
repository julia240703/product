<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function motorTypes()
    {
        return $this->hasMany(MotorType::class, 'category_id');
    }

    public function motors()
    {
        return $this->hasMany(Motor::class, 'category_id');
    }

    public function motorPart()
    {
        return $this->hasMany(MotorPart::class, 'category_id');
    }

    public function motorAccessory()
    {
        return $this->hasMany(MotorAccessory::class, 'category_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorCategory extends Model
{
    use HasFactory;

    public function motors()
    {
        return $this->hasMany(Motor::class);
    }
    
    protected $fillable = [
        'name',
    ];
}

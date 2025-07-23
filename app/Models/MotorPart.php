<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorPart extends Model
{
    use HasFactory;

    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }

    protected $fillable = [
        'motor_id',
        'name',
        'price',
        'image_url',
        'compatibility_notes',
    ];
}

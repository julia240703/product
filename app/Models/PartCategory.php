<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartCategory extends Model
{
    use HasFactory;

    public function parts()
    {
        return $this->hasMany(MotorPart::class);
    }

    protected $fillable = [
        'name',
    ];
}

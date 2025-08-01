<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function accessories()
    {
        return $this->hasMany(MotorAccessory::class, 'category_id');
    }
}

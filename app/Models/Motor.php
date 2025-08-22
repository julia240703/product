<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'motor_code_otr',
        'motor_code_credit',
        'wms_code',
        'category_id',
        'type_id',
        'description',
        'thumbnail',
        'accessory_thumbnail',
        'status',
    ];

    // Relasi ke Category (kategori motor/part/aksesoris)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Relasi ke MotorType
    public function type()
    {
        return $this->belongsTo(MotorType::class, 'type_id');
    }

    // Relasi ke fitur motor
    public function features()
    {
        return $this->hasMany(MotorFeature::class);
    }

    // Relasi ke warna motor
    public function colors()
    {
        return $this->hasMany(MotorColor::class);
    }

    // Relasi ke spesifikasi motor
    public function specifications()
    {
        return $this->hasMany(MotorSpecification::class);
    }

    // Relasi ke sparepart motor
    public function parts()
    {
        return $this->hasMany(MotorPart::class);
    }

    // Relasi ke aksesoris motor
    public function accessories()
    {
        return $this->hasMany(MotorAccessory::class);
    }
}
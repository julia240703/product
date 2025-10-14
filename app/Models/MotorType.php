<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'cover_image',
    ];

    /** Tipe → Kategori */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /** Tipe → banyak Motor (FK: motors.motor_type_id) */
    public function motors()
    {
        return $this->hasMany(Motor::class, 'type_id');
    }

    /** (Opsional) URL cover image siap pakai */
    public function getCoverImageUrlAttribute(): string
    {
        if (!$this->cover_image) {
            return asset('placeholder.png');
        }
        $p = ltrim($this->cover_image, '/');
        return str_starts_with($p, 'http')
            ? $this->cover_image
            : (str_starts_with($p, 'storage/')
                ? asset($p)
                : asset('storage/'.$p));
    }
}
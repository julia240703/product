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
        'feature_thumbnail',
        'status',
        'is_new',
        'spin_gif',
    ];

    protected $casts = [
        'is_new' => 'boolean', 
    ];

    public function getHasSpinAttribute(): bool
    {
        return !empty($this->spin_gif);
    }

    // ======================
    // Relations
    // ======================

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function type()
    {
        return $this->belongsTo(MotorType::class, 'type_id');
    }

    public function features()
    {
        return $this->hasMany(MotorFeature::class);
    }

    public function colors()
    {
        return $this->hasMany(MotorColor::class);
    }

    public function specifications()
    {
        return $this->hasMany(MotorSpecification::class);
    }

    public function parts()
    {
        return $this->hasMany(MotorPart::class);
    }

    public function accessories()
    {
        return $this->hasMany(MotorAccessory::class);
    }

    // ======================
    // Accessors (Best Practice)
    // ======================

    /**
     * URL thumbnail prioritas:
     * 1) kolom 'thumbnail'
     * 2) gambar warna pertama (colors->first()->image)
     * 3) placeholder
     */
    public function getThumbUrlAttribute(): string
    {
        if (!empty($this->thumbnail)) {
            return asset('storage/'.$this->thumbnail);
        }

        // pastikan relasi colors di-eager-load agar tidak N+1 (pakai with('colors'))
        $firstColorImage = optional($this->colors->first())->image;
        if ($firstColorImage) {
            return asset('storage/'.$firstColorImage);
        }

        return asset('placeholder.png');
    }

    /**
     * Teks harga minimum terformat (fallback ke 0 jika belum ada).
     * Pastikan kolom price_min ada di tabel motors (nullable juga boleh).
     */
    public function getPriceTextAttribute(): string
    {
        $min = $this->price_min ?? 0;
        return 'Rp '.number_format($min, 0, ',', '.');
    }

public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }
}
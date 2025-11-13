<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // <— TAMBAHKAN

class Motor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'motor_code_otr',
        'motor_code_credit',
        'wms_code',
        'price',
        'category_id',
        'type_id',
        'description',
        'motor_url',
        'thumbnail',
        'accessory_thumbnail',
        'accessory_url',
        'feature_thumbnail',
        'status',
        'is_new',
        'spin_gif',
        'parts_pdf',
    ];

    protected $casts = [
        'is_new' => 'boolean',
    ];

    // ======================
    // Accessors
    // ======================

    public function getPartsPdfUrlAttribute(): ?string
    {
        if (!$this->parts_pdf) return null;

        // normalisasi
        $p = ltrim($this->parts_pdf, '/');

        // DATA LAMA: sudah berawalan "storage/"
        if (Str::startsWith($p, 'storage/')) {
            return asset($p); // -> /storage/...
        }

        // DATA BARU: hanya "pdf/xxx.pdf" (relatif disk public)
        return asset('storage/'.$p);
    }

    public function getHasSpinAttribute(): bool
    {
        return !empty($this->spin_gif);
    }

    public function getThumbUrlAttribute(): string
    {
        if (!empty($this->thumbnail)) {
            return asset('storage/'.$this->thumbnail);
        }
        $firstColorImage = optional($this->colors->first())->image;
        if ($firstColorImage) {
            return asset('storage/'.$firstColorImage);
        }
        return asset('placeholder.png');
    }

    public function getPriceTextAttribute(): string
    {
        $price = $this->price ?? 0;
        return 'Rp '.number_format($price, 0, ',', '.');
    }

    // ======================
    // Scopes
    // ======================

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
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

    public function creditHeaders()
    {
        return $this->hasMany(CreditHeader::class);
    }

    public function latestCreditHeader()
    {
        return $this->hasOne(CreditHeader::class)->latestOfMany('valid_from');
    }
}
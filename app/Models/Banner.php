<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner_template_id',
        'title',
        'image_path',
        'status',
        'order'
    ];

    // Relationship dengan BannerTemplate
    public function bannerTemplate()
    {
        return $this->belongsTo(BannerTemplate::class, 'banner_template_id');
    }

    // Scope untuk status aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk order berdasarkan urutan
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}

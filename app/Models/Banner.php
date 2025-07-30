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

    public function template()
    {
        return $this->belongsTo(BannerTemplate::class, 'banner_template_id');
    }
}

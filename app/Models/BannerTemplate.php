<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function banners()
    {
        return $this->hasMany(Banner::class, 'banner_template_id');
    }
}
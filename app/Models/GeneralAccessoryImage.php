<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralAccessoryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_accessory_id',
        'image',
        'caption',
        'sort',
    ];

    public function accessory() {
        return $this->belongsTo(GeneralAccessory::class, 'general_accessory_id');
    }
}

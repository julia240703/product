<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralAccessoryVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_accessory_id',
        'variant_name',
        'sku',
        'color',
        'price',
        'stock',
        'image',
    ];

    public function accessory() {
        return $this->belongsTo(GeneralAccessory::class, 'general_accessory_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'tax_number',
        'price_status',
        'area_id',
        'city_id',
        'ranking',
        'service',
        'address',
        'latitude',
        'longitude',
        'url',
        'phone',
        'phone2',
        'phone3',
        'fax',
        'wanda_dealer_id',
        'wanda_api_key',
        'wanda_api_secret',
        'ahass_code',
        'order',
    ];

    public function area()
    {
        return $this->belongsTo(BranchLocation::class, 'area_id')->where('type', 'area');
    }

    public function city()
    {
        return $this->belongsTo(BranchLocation::class, 'city_id')->where('type', 'kota');
    }
}
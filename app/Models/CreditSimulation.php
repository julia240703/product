<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSimulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'province',
        'city',
        'motor_category',
        'motor_type',
        'motor_variant',
        'otr_price',
        'dp_amount',
        'tenor_months',
    ];
}

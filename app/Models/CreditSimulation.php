<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSimulation extends Model
{
    use HasFactory;

    protected $table = 'credit_simulations';

    protected $fillable = [
        'category_id',
        'motor_type_id',
        'motorcycle_variant',
        'otr_price',
        'minimum_dp',
        'loan_term',
        'interest_rate',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function motorType()
    {
        return $this->belongsTo(MotorType::class, 'motor_type_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_location', 'id','branch_id');
    }
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'follow_up',
        'national_id',
        'address',
        'domicile',
        'birthdate',
        'gender',
        'marital_status',
        'religion',
        'applied_position',
        'landline_phone',
        'mobile_number',
        'cv',
        'branch_location',
    ];

}
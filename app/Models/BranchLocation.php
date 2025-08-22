<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // 'area' or 'city'
        'name',
    ];

    public function branchesAsArea()
    {
        return $this->hasMany(Branch::class, 'area_id');
    }

    public function branchesAsCity()
    {
        return $this->hasMany(Branch::class, 'city_id');
    }
}
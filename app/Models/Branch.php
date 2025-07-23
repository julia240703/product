<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public function services()
    {
        return $this->hasMany(BranchService::class);
    }

    protected $fillable = [
        'area',
        'name',
        'city',
        'address',
        'phone',
    ];
}

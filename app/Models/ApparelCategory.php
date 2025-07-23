<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApparelCategory extends Model
{
    use HasFactory;

    public function apparels()
    {
        return $this->hasMany(Apparel::class);
    }

    protected $fillable = [
        'name',
    ];
}

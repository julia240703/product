<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',         // kolom nama tipe motor
        'category_id',  // foreign key ke tabel kategori
    ];

    /**
     * Relasi ke kategori motor
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
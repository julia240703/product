<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    protected $fillable = [
        'quiz_id',
        'image',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'is_correct',
    ];
}
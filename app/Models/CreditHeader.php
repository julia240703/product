<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditHeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'motor_id',
        'credit_provider_id', // nullable
        'valid_from',
        'valid_to',
        'note',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to'   => 'date',
    ];

    // ================
    // Relations
    // ================

    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }

    public function provider()
    {
        return $this->belongsTo(CreditProvider::class, 'credit_provider_id');
    }

    public function items()
    {
        return $this->hasMany(CreditItem::class, 'header_id');
    }

    // Header terbaru untuk motor tertentu biasanya diambil via relasi di Motor:
    // $motor->latestCreditHeader
    // (lihat method latestCreditHeader() yang sudah kita buat di model Motor)

    // ================
    // Scopes
    // ================

    // Header yang masih berlaku pada tanggal tertentu (default: hari ini)
    public function scopeActive($q, $date = null)
    {
        $date = $date ?: now()->toDateString();
        return $q->where(function ($qq) use ($date) {
            $qq->whereNull('valid_from')->orWhere('valid_from', '<=', $date);
        })->where(function ($qq) use ($date) {
            $qq->whereNull('valid_to')->orWhere('valid_to', '>=', $date);
        });
    }
}
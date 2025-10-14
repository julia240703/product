<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'header_id',
        'dp_amount',      // rupiah
        'tenor_months',   // 11, 17, 23, 27, 29, 33, 35, 41
        'installment',    // rupiah/bulan
    ];

    // ================
    // Relations
    // ================

    public function header()
    {
        return $this->belongsTo(CreditHeader::class, 'header_id');
    }

    // Lewat header -> bisa akses motor:
    // $item->header->motor

    // ================
    // Accessors (opsional)
    // ================

    public function getDpTextAttribute(): string
    {
        return 'Rp '.number_format((int) $this->dp_amount, 0, ',', '.');
    }

    public function getInstallmentTextAttribute(): string
    {
        return 'Rp '.number_format((int) $this->installment, 0, ',', '.');
    }
}
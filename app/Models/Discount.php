<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'value',
        'max_discount',
        'min_transaction',
        'start_date',
        'quota',
        'status',
        'expired_date',
    ];

    protected $casts = [
        'value' => 'double',
    ];

    public function getStatusAttribute($value)
    {
        $today = now()->format('Y-m-d');
        
        // Jika dari database sudah di-set inactive secara manual
        if ($value === 'inactive') {
            return $value;
        }

        // Cek upcoming
        if ($this->start_date && $today < $this->start_date) {
            return 'upcoming';
        }

        // Cek expired
        if ($this->expired_date && $today > $this->expired_date) {
            return 'expired';
        }

        return 'active';
    }
}

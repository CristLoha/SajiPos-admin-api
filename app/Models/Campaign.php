<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'discount_type',
        'discount_value',
        'is_active',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}

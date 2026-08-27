<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cashier_id',
        'transaction_time',
        'sub_total',
        'discount_id',
        'discount_name',
        'discount_amount',
        'shipping_cost',
        'service_charge',
        'tax',
        'total',
        'payment_method',
        'midtrans_order_id',
        'payment_token',
        'receipt_token',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->receipt_token)) {
                $model->receipt_token = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

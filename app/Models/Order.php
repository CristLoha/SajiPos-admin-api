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

    protected $appends = ['payment_details'];

    public function getPaymentDetailsAttribute()
    {
        $paymentMethod = strtolower($this->payment_method);
        
        if ($paymentMethod === 'qris') {
            return [
                'transaction_id' => $this->midtrans_order_id,
                'payment_type' => 'qris',
                'qr_string' => $this->payment_token,
                'qr_image_url' => $this->payment_token ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($this->payment_token) : null,
            ];
        } elseif (in_array($paymentMethod, ['transfer', 'bank_transfer'])) {
            return [
                'transaction_id' => $this->midtrans_order_id,
                'payment_type' => 'bank_transfer',
                'snap_token' => $this->payment_token,
            ];
        }

        return null;
    }

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

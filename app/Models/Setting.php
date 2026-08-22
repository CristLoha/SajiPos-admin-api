<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'shop_name',
        'shop_address',
        'shop_phone',
        'show_phone_on_receipt',
        'show_address_on_receipt',
        'show_logo_on_receipt',
        'logo_url',
        'shipping_fee',
        'include_shipping_in_tax',
        'service_fee',
        'include_service_fee_in_tax',
        'tax_percentage',
    ];
    
    protected $casts = [
        'shipping_fee' => 'float',
        'include_shipping_in_tax' => 'boolean',
        'service_fee' => 'float',
        'include_service_fee_in_tax' => 'boolean',
        'tax_percentage' => 'float',
        'show_phone_on_receipt' => 'boolean',
        'show_address_on_receipt' => 'boolean',
        'show_logo_on_receipt' => 'boolean',
    ];
}

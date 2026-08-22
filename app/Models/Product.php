<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
    }

    protected $appends = ['is_campaign_active', 'discount_price'];

    public function getIsCampaignActiveAttribute()
    {
        $campaign = $this->campaigns()->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
        return $campaign ? true : false;
    }

    public function getDiscountPriceAttribute()
    {
        $campaign = $this->campaigns()->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if ($campaign) {
            if ($campaign->discount_type == 'percentage' || $campaign->discount_type == 'percent') {
                return $this->price - ($this->price * $campaign->discount_value / 100);
            } else {
                return max(0, $this->price - $campaign->discount_value);
            }
        }
        return null;
    }

    protected static function booted()
    {
        static::saving(function ($product) {
            $product->status = $product->stock > 0;
        });
    }
}


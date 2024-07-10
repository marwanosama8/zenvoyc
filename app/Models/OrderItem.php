<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'one_time_product_id',
        'quantity',
        'currency_id',
        'price_per_unit',
        'price_per_unit_after_discount',
        'discount_per_unit',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function oneTimeProduct()
    {
        return $this->belongsTo(OneTimeProduct::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsPrices extends Model
{
    protected $table = 'products_prices';

    protected $fillable = ['product_id', 'purchase_price', 'sale_price', 'effective_date', 'warehouse_id'];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = ['name', 'barcode', 'warehouse_id'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouses::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductsPrices::class, 'product_id');
    }

    public function entries()
    {
        return $this->hasMany(ProductsEntries::class, 'product_id');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class, 'product_id');
    }

    public function latestPrice()
    {
        return $this->hasOne(ProductsPrices::class, 'product_id')->latestOfMany('effective_date');
    }
}

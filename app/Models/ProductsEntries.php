<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsEntries extends Model
{
    protected $table = 'products_entries';

    protected $fillable = ['product_id', 'user_id', 'quantity', 'total_cost', 'unit_cost', 'entry_date', 'warehouse_id'];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

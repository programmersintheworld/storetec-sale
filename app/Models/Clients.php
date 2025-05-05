<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use App\Models\Sale;

class Clients extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'warehouse_id'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouses::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }
}

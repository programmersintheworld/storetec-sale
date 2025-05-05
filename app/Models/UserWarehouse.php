<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWarehouse extends Model
{
    protected $table = 'user_warehouses';

    protected $fillable = ['user_id', 'warehouse_id'];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouses::class);
    }
}

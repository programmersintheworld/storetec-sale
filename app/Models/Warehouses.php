<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    protected $fillable = ['name', 'user_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_warehouses', 'warehouse_id', 'user_id');
    }

    public function clients()
    {
        return $this->hasMany(Clients::class);
    }

    public function products()
    {
        return $this->hasMany(Products::class);
    }
}

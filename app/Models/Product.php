<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'internal_id', 'unit', 'enabled'];

    public function inventory(){
        return $this->hasMany(Inventory::class);
    }

    public function transaction_details(){
        return $this->hasMany(TransactionDetail::class);
    }
}

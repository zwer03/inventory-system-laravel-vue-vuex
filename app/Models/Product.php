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

    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $model->user_id = auth()->user()->id;
            $model->enabled = 1;
        });

        static::updating(function($model)
        {
            $model->user_id = auth()->user()->id;
        });
    }
}

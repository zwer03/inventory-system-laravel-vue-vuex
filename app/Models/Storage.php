<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $fillable = ['name', 'warehouse_id', 'enabled'];

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
    public function products(){
        return $this->hasMany(Product::class);
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

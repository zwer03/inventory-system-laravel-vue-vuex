<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'short_name', 'enabled'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
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

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'address', 'role', 'enabled'];

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

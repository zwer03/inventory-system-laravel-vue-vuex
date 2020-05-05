<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
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
    public function storage(){
        return $this->hasMany(Storage::class);
    }
}

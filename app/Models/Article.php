<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'body'];

    public function article(){
        return $this->belongsTo(User::class);
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $model->user_id = auth()->user()->id;
        });

        static::updating(function($model)
        {
            $model->user_id = auth()->user()->id;
        });
    }
}

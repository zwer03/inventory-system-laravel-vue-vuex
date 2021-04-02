<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'address', 'enabled'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function warehouse(){
        return $this->hasMany(Warehouse::class);
    }
}

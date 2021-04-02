<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function storage(){
        return $this->hasMany(Storage::class);
    }
}

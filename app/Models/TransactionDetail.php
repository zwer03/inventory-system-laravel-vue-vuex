<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function storage(){
        return $this->belongsTo(Storage::class);
    }
    public function old_storage(){
        return $this->belongsTo(Storage::class, 'old_storage_id');
    }
}

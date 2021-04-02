<?php
/*
    transaction_type :
    in, out, adjustment, lost, transfer

    status:
    5 = ongoing
    10 = completed
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public static $ongoing = 5;
    public static $completed = 10;

    public function transaction_details(){
        return $this->hasMany(TransactionDetail::class);
    }

    public function processed_by(){
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function validated_by(){
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
}

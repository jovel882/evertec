<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];    
    protected $guarded = ['id','uuid'];
    /**
    * Relacion con la orden.
    *
    * @return Relacion.
    */    
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    /**
    * Relacion con los estado.
    *
    * @return Relacion.
    */    
    public function transaction_states()
    {
        return $this->hasMany('App\Models\TransactionState');
    }        
}

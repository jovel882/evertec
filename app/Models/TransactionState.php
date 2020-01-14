<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionState extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];    
    protected $guarded = ['id'];
    /**
    * Relacion con la transaccion.
    *
    * @return Relacion.
    */    
    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }
}

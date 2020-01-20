<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\CreateTransactionState;

class TransactionState extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => CreateTransactionState::class,
    ];

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

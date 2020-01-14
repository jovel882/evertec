<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Manejo del evento creating de las transacciones.
     *
     * @param  \App\Models\Transaction  $transaction Modelo con la transaccion.
     * @return void
     */
    public function creating(Transaction $transaction)
    {
        $transaction->uuid=$transaction->uuid??(string) \Illuminate\Support\Str::uuid();
    }
}

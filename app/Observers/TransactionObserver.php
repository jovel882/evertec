<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Manejo del evento creating de las transacciones.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function creating(Transaction $transaction)
    {
        $transaction->uuid=(string) \Illuminate\Support\Str::uuid();
    }
}

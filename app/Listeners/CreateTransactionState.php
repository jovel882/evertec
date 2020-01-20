<?php

namespace App\Listeners;

use App\Events\CreateTransactionState as EventCreateTransactionState;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\TransactionsStatusCreated;

class CreateTransactionState
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EventCreateTransactionState  $event
     * @return void
     */
    public function handle(EventCreateTransactionState $event)
    {
        $event->transactionState
            ->transaction
            ->order
            ->user
            ->notify(new TransactionsStatusCreated($event->transactionState));
    }
}

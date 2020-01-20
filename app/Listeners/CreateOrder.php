<?php

namespace App\Listeners;

use App\Events\CreateOrder as EventCreateOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\OrderCreated;

class CreateOrder
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
     * @param  EventCreateOrder  $event
     * @return void
     */
    public function handle(EventCreateOrder $event)
    {
        $event->order
            ->user->notify(new OrderCreated($event->order));
    }
}

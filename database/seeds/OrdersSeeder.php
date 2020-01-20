<?php

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dispatcher = Order::getEventDispatcher();
        Order::unsetEventDispatcher();
        factory(Order::class, 50)->create();
        Order::setEventDispatcher($dispatcher);
    }
}

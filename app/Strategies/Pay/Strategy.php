<?php

namespace App\Strategies\Pay;

use App\Models\Order;

interface Strategy
{
    public function pay(Order $order);
}

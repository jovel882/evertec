<?php

namespace App\Strategies\Pay;

use App\Strategies\Pay\Strategy;
use App\Models\Order;

class  Context
{
    private $strategy;

    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }
    public static function create(Strategy $strategy)
    {
        return new static($strategy);
    }
    public function pay(Order $order)
    {
        return $this->strategy->pay($order);
    }
}

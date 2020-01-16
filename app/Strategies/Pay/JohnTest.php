<?php
namespace App\Strategies\Pay;

use App\Strategies\Pay\Strategy;
use App\Models\Order;

class JohnTest implements Strategy
{  
    public function pay(Order $order)
    {
        return "JohnTest";        
    }
}
?>
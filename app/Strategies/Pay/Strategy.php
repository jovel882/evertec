<?php

namespace App\Strategies\Pay;

use App\Models\Order;
use App\Models\Transaction;

interface Strategy
{
    public function pay(Order $order);
    public function getInfoPay(Transaction $transaction);
}

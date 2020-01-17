<?php

namespace App\Strategies\Pay;

use App\Strategies\Pay\Strategy;
use App\Models\Order;
use App\Models\Transaction;
use App\Strategies\Pay\PlaceToPay;
use App\Strategies\Pay\JohnTest;

class  Context
{
    /**
     * @var array $paymentMethodsEnable Metodos de pagos habilitados.
     */   
    private static $paymentMethodsEnable=[
        "place_to_pay" => PlaceToPay::class,
        "john_test" => JohnTest::class,
    ];    
    private $strategy;

    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }
    public static function create(String $strategy)
    {
        if (isset(self::$paymentMethodsEnable[$strategy])) {
            return new static(new self::$paymentMethodsEnable[$strategy]);
        }

        return false;
    }
    public function pay(Order $order)
    {
        return $this->strategy->pay($order);
    }
    public function getInfoPay(Transaction $transaction)
    {
        return $this->strategy->getInfoPay($transaction);
    }
}

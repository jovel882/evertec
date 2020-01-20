<?php

namespace App;

use App\Strategies\Pay\Context;
use App\Strategies\Pay\PlaceToPay;
use App\Strategies\Pay\JohnTest;
use App\Models\Order;
use App\Models\Transaction;

class Payment
{

    /**
     * @var array $paymentMethodsEnable Metodos de pagos habilitados.
     */
    private static $paymentMethodsEnable=[
        "place_to_pay" => PlaceToPay::class,
        "john_test" => JohnTest::class,
    ];


    private function createStrategy(string $typePay)
    {
        try {
            if (isset(self::$paymentMethodsEnable[$typePay])) {
                if ($typePay == "place_to_pay") {
                    return Context::create(
                        new self::$paymentMethodsEnable[$typePay](
                            resolve('Dnetix\Redirection\PlacetoPay'),
                            new Transaction()
                        )
                    );
                }
    
                return Context::create(new self::$paymentMethodsEnable[$typePay]);
            }
        } catch (\Dnetix\Redirection\Exceptions\PlacetoPayException $e) {
            \Log::info($e->getMessage());
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        return false;
    }
    public function pay(string $typePay, Order $order)
    {
        if ($staregy = $this->createStrategy($typePay)) {
            return $staregy->pay($order);
        }

        return false;
    }
    public function getInfoPay(Transaction $transaction)
    {
        if ($staregy = $this->createStrategy($transaction->gateway)) {
            return $staregy->getInfoPay($transaction);
        }

        return false;
    }
}

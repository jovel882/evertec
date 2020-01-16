<?php
namespace App\Strategies\Pay;

use App\Strategies\Pay\Strategy;
use App\Models\Order;
use Dnetix\Redirection\PlacetoPay as PlacetoPayLib;
use App\Traits\PaymentTrait;

class PlaceToPay extends PlacetoPayLib implements Strategy
{
    use PaymentTrait;

    public function __construct()
    {
        parent::__construct(
            [
            'login' => env('PLACE_TO_PAY_LOGIN'),
            'tranKey' => env('PLACE_TO_TRAN_KEY'),
            'url' => env('PLACE_TO_TRAN_URL'),
            ]
        );
    }          
    public function pay(Order $order)
    {
        $uuid=$this->getUuid();
        $request = [
            "locale" => "es_CO",
            "buyer" => $this->getBuyer(),
            "payment" => [
                "reference" => $this->getReference($order->id),
                "description" => "Compra de ".config('config.product_name'),
                "amount" => [
                    "currency" => "COP",
                    "total" => $order->total,
                    "taxes" => [
                        [
                            "kind" => "iva",
                            "amount" => 0.00
                        ]
                    ]
                ],
                "items" => [
                    [
                        "name" => config('config.product_name'),
                        "price" => config('config.product_price')
                    ]                    
                ],
                "allowPartial" => false,
            ],
            "expiration" => \Carbon\Carbon::now()->addMinutes(
                config('config.expired_minutes_PTP')
                )->format("c"),
            "ipAddress" => request()->ip(),
            "userAgent" => request()->header('user-agent'),
            "returnUrl" => route("transactions.receive",["gateway" => "place_to_pay",'uuid' => $uuid]),
            "cancelUrl" => route("orders.show", ['order' => $order->id]),
            "skipResult" => false,
            "noBuyerFill" => false,
            "captureAddress" => false,
            "paymentMethod" => null
        ];
    }
    private function getBuyer(): Array
    {
        $user = auth()->user();

        return [
            "name" => $user->name,
            "email" => $user->email,
            "mobile" => $user->phone,            
        ];
    }    
}
?>
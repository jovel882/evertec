<?php
namespace App\Strategies\Pay;

use App\Strategies\Pay\Strategy;
use App\Models\Order;
use App\Models\Transaction;
use Dnetix\Redirection\PlacetoPay as PlacetoPayLib;
use Dnetix\Redirection\Entities\Status;
use App\Traits\PaymentTrait;

class PlaceToPay implements Strategy
{
    use PaymentTrait;

    /**
     * @var array $statusMap Estados mapeados.
     */
    public $statusMap;
    /**
     * @var Transaction $transaction Modelo de transaccion.
     */
    public $transaction;
    /**
     * @var PlacetoPayLib $placeToPay Objeto place to pay.
     */
    public $placeToPay;

    public function __construct(PlacetoPayLib $placeToPay, Transaction $transaction)
    {
        $this->placeToPay = $placeToPay;
        $this->transaction = $transaction;
        $this->mapStatus();
    }
    private function mapStatus()
    {
        $statusMap=&$this->statusMap;
        array_map(function ($state) use (&$statusMap) {
            switch ($state) {
                case 'APPROVED':
                    $statusMap[$state] = "PAYED";
                    break;
                case 'ERROR':
                case 'FAILED':
                case 'REJECTED':
                    $statusMap[$state] = "REJECTED";
                    break;
                case 'REFUNDED':
                    $statusMap[$state] = "REFUNDED";
                    break;
                case 'PENDING_VALIDATION':
                case 'PENDING':
                    $statusMap[$state] = "PENDING";
                    break;
                default:
                    $statusMap[$state] = "CREATED";
                    break;
            }
        }, Status::validStatus());
    }
    public function pay(Order $order)
    {
        \DB::beginTransaction();
        try{
            $response = $this->createPay($order);
            \DB::commit();
            return (object) [
                'success' => true,
                'url' => $response->processUrl(),
            ];
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            \DB::rollback();
            return (object) [
                'success' => false,
                'exception' => $e,
            ];
        }
    }
    public function createPay(Order $order){
        $uuid = $this->getUuid();
        $reference = $this->getReference($order->id);
        $request = $this->getRequestData($order, $reference, $uuid);
        $response = $this->placeToPay->request($request);

        if (! $response->isSuccessful()) {
            throw new \Exception("Se genero un error al crear la transaccion en placetopay (".$response->status()->message().").");
        }
        
        $transaction = $this->transaction->store([
            'order_id' => $order->id,
            'uuid' => $uuid,
            'current_status' => 'CREATED',
            'reference' => $reference,
            'url' => $response->processUrl(),
            'requestId' => $response->requestId(),
            'gateway' => 'place_to_pay',
        ]);

        if (! $transaction) {
            throw new \Exception('Se genero un error al almacenar la transaccion.');
        }
                                        
        if (! $transaction->attachStates(
            [
                [
                    'status' => 'CREATED',
                    'data' => json_encode($response->toArray()),
                ]
            ]
        )) {
            throw new \Exception('Se genero un error al almacenar el estado de la transaccion.');
        }
        
        return $response;
    }
    public function getInfoPay(Transaction $transaction)
    {
        try {
            $response = $this->placeToPay->query($transaction->requestId);

            $transaction_states = $transaction->transaction_states->first();

            $status = $this->getStatus($response);
            if (!$status) {
                throw new \Exception('El estado recibido no se identifica.');
            }
            if ($transaction_states && $transaction_states->status != $status) {
                if (! $transaction->edit(
                    [
                        'current_status' => $status,
                    ]
                )) {
                    throw new \Exception('Se genero un error al actualizar la transaccion.');
                }
                if (! $transaction->attachStates(
                    [
                        [
                            'status' => $status,
                            'data' => json_encode($response->toArray()),
                        ]
                    ]
                )) {
                    throw new \Exception('Se genero un error al almacenar el estado de la transaccion.');
                }
            }
            return (Object) [
                "success" => true,
                "data" => [
                    "status" => $this->getStatus($response),
                    "message" => $response->status()->message(),
                ]
            ];
        } catch (Exception $e) {
            return (Object) [
                "success" => false,
                "exception" => $e
            ];
        }
    }
    private function getStatus($response): String
    {
        if (!isset($this->statusMap[$response->status()->status()])) {
            return false;
        }
        
        return $this->statusMap[$response->status()->status()];
    }
    private function getRequestData(Order $order, $reference, $uuid): array
    {
        $urlRecive = route("transactions.receive", ["gateway" => "place_to_pay",'uuid' => $uuid]);
        return [
            "locale" => "es_CO",
            "buyer" => $this->getBuyer(),
            "payment" => [
                "reference" => $reference,
                "description" => "Compra de (".$order->quantity.") ".config('config.product_name'),
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
            "returnUrl" => $urlRecive,
            "cancelUrl" => $urlRecive,
            "skipResult" => false,
            "noBuyerFill" => false,
            "captureAddress" => false,
            "paymentMethod" => null
        ];
    }
    private function getBuyer(): array
    {
        $user = auth()->user();

        return [
            "name" => $user->name,
            "email" => $user->email,
            "mobile" => $user->phone,
        ];
    }
}

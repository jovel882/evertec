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
     * @var array $statusMap Estados mapeados para las ordenes.
     */
    public $statusOrderMap;
    /**
     * @var Transaction $transaction Modelo de transaccion.
     */
    public $transaction;
    /**
     * @var PlacetoPayLib $placeToPay Objeto place to pay.
     */
    public $placeToPay;

    /**
     * Constructor de metodo de pago.
     *
     * @param  PlacetoPayLib $placeToPay  Objeto de la libreria para gestionar las transacciones.
     * @param  Transaction $transaction  Modelo de transacciones.
     * @return void
     */
    public function __construct(PlacetoPayLib $placeToPay, Transaction $transaction)
    {
        $this->placeToPay = $placeToPay;
        $this->transaction = $transaction;
        $this->mapStatus();
    }

    /**
     * Crea el arreglo para hacer la conversion de los estados recibidos con los permitidos.
     *
     * @return void
     */
    private function mapStatus()
    {
        $statusMap = &$this->statusMap;
        $statusOrderMap = &$this->statusOrderMap;
        array_map(function ($state) use (&$statusMap, &$statusOrderMap) {
            switch ($state) {
                case 'APPROVED':
                    $statusMap[$state] = "PAYED";
                    $statusOrderMap[$state] = "PAYED";
                    break;
                case 'ERROR':
                case 'FAILED':
                case 'REJECTED':
                    $statusMap[$state] = "REJECTED";
                    $statusOrderMap[$state] = "CREATED";
                    break;
                case 'REFUNDED':
                    $statusMap[$state] = "REFUNDED";
                    $statusOrderMap[$state] = "CREATED";
                    break;
                case 'PENDING_VALIDATION':
                case 'PENDING':
                    $statusMap[$state] = "PENDING";
                    $statusOrderMap[$state] = "CREATED";
                    break;
                default:
                    $statusMap[$state] = "CREATED";
                    $statusOrderMap[$state] = "CREATED";
                    break;
            }
        }, Status::validStatus());
    }
    /**
     * Crea la transaccion en placetopay.
     *
     * @param  Order $order  Modelo de orden.
     * @return array Con el estado de la crecion de la transaccion.
     */
    public function pay(Order $order)
    {
        \DB::beginTransaction();
        try {
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
        } catch (\Dnetix\Redirection\Exceptions\PlacetoPayException $e) {
            \Log::info($e->getMessage());
            \DB::rollback();
            return (object) [
                'success' => false,
                'exception' => $e,
            ];
        }
    }

    /**
     * Realiza los procesos para crear la transaccion.
     *
     * @param  Order $order  Modelo de orden.
     * @return Exception|RedirectResponse Con una excepción si ocurre o conel la respuesta de la crecion de la transaccion.
     */
    public function createPay(Order $order)
    {
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

    /**
     * Valida y actualiza el estado de una transaccion con Placetopay.
     *
     * @param  Transaction $order  Modelo de orden.
     * @return array Con la respuesta de la consulta de la transaccion.
     */
    public function getInfoPay(Transaction $transaction)
    {
        try {
            $response = $this->placeToPay->query($transaction->requestId);
            $status = $this->getStatus($response);
            
            if (!$status) {
                throw new \Exception('El estado recibido no se identifica.');
            }

            if ($transaction->getAttributeValue('current_status') != $status) {
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

                if (! $transaction->updateOrder(
                    [
                        'status' => $this->getOrderStatus($response),
                    ]
                )) {
                    throw new \Exception('Se genero un error al actualizar el estado de la orden.');
                }
            }

            return (Object) [
                "success" => true,
                "data" => [
                    "status" => $this->getStatus($response),
                    "message" => $response->status()->message(),
                ]
            ];
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return (Object) [
                "success" => false,
                "exception" => $e
            ];
        } catch (\Dnetix\Redirection\Exceptions\PlacetoPayException $e) {
            \Log::info($e->getMessage());
            \DB::rollback();
            return (object) [
                'success' => false,
                'exception' => $e,
            ];
        }
    }

    /**
     * Obtiene la conversion del estado de la respuesta recibida de place to pay.
     *
     * @param  RedirectInformation $response  Modelo de orden.
     * @return bool|string false indicando que el estado es desconocido o el texto del estado.
     */
    private function getStatus($response): String
    {
        if (!isset($this->statusMap[$response->status()->status()])) {
            return false;
        }
        
        return $this->statusMap[$response->status()->status()];
    }
    
    /**
     * Obtiene la conversion del estado de la respuesta recibida de place to pay a los de las ordenes.
     *
     * @param  RedirectInformation $response  Modelo de orden.
     * @return bool|string false indicando que el estado es desconocido o el texto del estado.
     */
    private function getOrderStatus($response): String
    {
        if (!isset($this->statusOrderMap[$response->status()->status()])) {
            return false;
        }
        
        return $this->statusOrderMap[$response->status()->status()];
    }

    /**
     * Obtiene el arreglo para crear la transaccion en la pasarela.
     *
     * @param  Order $order  Modelo de orden.
     * @param  string $reference Texto de la referencia.
     * @param  string $uuid  Texto del UUID.
     * @return array Con el arreglo.
     */
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

    /**
     * Obtiene el arreglo con los datos del pagador.
     *
     * @return array Con el arreglo.
     */
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

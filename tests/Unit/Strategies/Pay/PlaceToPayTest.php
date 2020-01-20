<?php

namespace Tests\Unit\Strategies\Pay;

use Tests\TestCase;
use Dnetix\Redirection\PlacetoPay as LibPlacetoPay;
use App\Models\Order;
use App\Models\Transaction;
use App\User;
use App\Strategies\Pay\PlaceToPay as EstrategyPlaceToPay;
use Dnetix\Redirection\Message\RedirectResponse;
use Dnetix\Redirection\Message\RedirectInformation;

class PlaceToPayTest extends TestCase
{
    protected $stubPlaceToPay;
    protected $stubOrder;
    protected $stubUser;
    protected $stubTransaction;

    protected function setUp() : void
    {
        parent::setUp();
        
        $this->stubPlaceToPay = $this->createMock(LibPlaceToPay::class);
        $this->stubOrder = $this->createMock(Order::class);
        $this->stubUser = $this->createMock(User::class);
        $this->stubTransaction = $this->createMock(Transaction::class);
    }

    /**
     *
     * @test
     * @return void
     */
    public function create_payment_and_receive_payment_request_creation_error()
    {
        $response = $this->returnRedirectResponse('FAILED');
        $this->stubPlaceToPay->method('request')
             ->willReturn($response);
        $this->actingAs($this->stubUser);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Se genero un error al crear la transaccion en placetopay (".$response->status()->message().").");
        
        $placeToPay = new EstrategyPlaceToPay($this->stubPlaceToPay, $this->stubTransaction);
        $placeToPay->createPay($this->stubOrder);
    }

    /**
     *
     * @test
     * @return void
     */
    public function create_payment_and_receive_transaction_creation_error()
    {
        $response = $this->returnRedirectResponse();
        $this->stubTransaction->method('store')
            ->willReturn(false);
        $this->stubPlaceToPay->method('request')
             ->willReturn($response);
        $this->actingAs($this->stubUser);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Se genero un error al almacenar la transaccion.');
        
        $placeToPay = new EstrategyPlaceToPay($this->stubPlaceToPay, $this->stubTransaction);
        $placeToPay->createPay($this->stubOrder);
    }

    /**
     *
     * @test
     * @return void
     */
    public function create_payment_and_receive_transaction_status_creation_error()
    {
        $response = $this->returnRedirectResponse();
        $this->stubTransaction->method('store')
            ->will($this->returnSelf());
        $this->stubTransaction->method('attachStates')
            ->willReturn(false);
        $this->stubPlaceToPay->method('request')
             ->willReturn($response);
        $this->actingAs($this->stubUser);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Se genero un error al almacenar el estado de la transaccion.');
        
        $placeToPay = new EstrategyPlaceToPay($this->stubPlaceToPay, $this->stubTransaction);
        $placeToPay->createPay($this->stubOrder);
    }

    /**
     *
     * @test
     * @return void
     */
    public function create_payment_and_receive_correct_response()
    {
        $response = $this->returnRedirectResponse('OK', 'La petición se ha procesado correctamente');
        $this->stubTransaction->method('store')
            ->will($this->returnSelf());
        $this->stubTransaction->method('attachStates')
            ->willReturn(true);
        $this->stubPlaceToPay->method('request')
             ->willReturn($response);
        $this->actingAs($this->stubUser);
        
        $placeToPay = new EstrategyPlaceToPay($this->stubPlaceToPay, $this->stubTransaction);
        $resposneReturn = $placeToPay->createPay($this->stubOrder);

        $this->assertEquals($resposneReturn, $response);
    }

    /**
     *
     * @test
     * @return void
     */
    public function check_payment_and_receive_unknown_status_error()
    {
        $response = $this->returnRedirectInformation('Other');
        $this->stubPlaceToPay->method('query')
             ->willReturn($response);
        
        $placeToPay = new EstrategyPlaceToPay($this->stubPlaceToPay, $this->stubTransaction);
        $resposneReturn = $placeToPay->getInfoPay($this->stubTransaction);

        $this->assertEquals($resposneReturn->success, false);
        $this->assertInstanceOf(\Exception::class, $resposneReturn->exception);
        $this->assertEquals($resposneReturn->exception->getMessage(), 'El estado recibido no se identifica.');
    }

    /**
     *
     * @test
     * @return void
     */
    public function check_payment_and_receive_transaction_status_update_error()
    {
        $response = $this->returnRedirectInformation();
        $this->stubTransaction->method('getAttributeValue')
             ->willReturn('CREATED');
        $this->stubTransaction->method('edit')
             ->willReturn(false);
        $this->stubPlaceToPay->method('query')
             ->willReturn($response);
        
        $placeToPay = new EstrategyPlaceToPay($this->stubPlaceToPay, $this->stubTransaction);
        $resposneReturn = $placeToPay->getInfoPay($this->stubTransaction);

        $this->assertEquals($resposneReturn->success, false);
        $this->assertInstanceOf(\Exception::class, $resposneReturn->exception);
        $this->assertEquals($resposneReturn->exception->getMessage(), 'Se genero un error al actualizar la transaccion.');
    }

    /**
     *
     * @test
     * @return void
     */
    public function check_payment_and_receive_transaction_status_creation_error()
    {
        $response = $this->returnRedirectInformation();
        $this->stubTransaction->method('edit')
             ->willReturn(true);
        $this->stubTransaction->method('getAttributeValue')
             ->willReturn('CREATED');
        $this->stubTransaction->method('attachStates')
             ->willReturn(false);
        $this->stubPlaceToPay->method('query')
             ->willReturn($response);
        
        $placeToPay = new EstrategyPlaceToPay($this->stubPlaceToPay, $this->stubTransaction);
        $resposneReturn = $placeToPay->getInfoPay($this->stubTransaction);

        $this->assertEquals($resposneReturn->success, false);
        $this->assertInstanceOf(\Exception::class, $resposneReturn->exception);
        $this->assertEquals($resposneReturn->exception->getMessage(), 'Se genero un error al almacenar el estado de la transaccion.');
    }

    /**
     *
     * @test
     * @return void
     */
    public function check_payment_and_receive_order_status_update_error()
    {
        $response = $this->returnRedirectInformation();
        $this->stubTransaction->method('edit')
             ->willReturn(true);
        $this->stubTransaction->method('getAttributeValue')
             ->willReturn('CREATED');
        $this->stubTransaction->method('attachStates')
             ->willReturn(true);
        $this->stubTransaction->method('updateOrder')
             ->willReturn(false);             
        $this->stubPlaceToPay->method('query')
             ->willReturn($response);
        
        $placeToPay = new EstrategyPlaceToPay($this->stubPlaceToPay, $this->stubTransaction);
        $resposneReturn = $placeToPay->getInfoPay($this->stubTransaction);

        $this->assertEquals($resposneReturn->success, false);
        $this->assertInstanceOf(\Exception::class, $resposneReturn->exception);
        $this->assertEquals($resposneReturn->exception->getMessage(), 'Se genero un error al actualizar el estado de la orden.');
    }

    /**
     *
     * @test
     * @return void
     */
    public function check_payment_and_receive_correct_response()
    {
        $response = $this->returnRedirectInformation();
        $this->stubTransaction->method('edit')
             ->willReturn(true);
        $this->stubTransaction->method('getAttributeValue')
             ->willReturn('CREATED');
        $this->stubTransaction->method('attachStates')
             ->willReturn(true);
        $this->stubTransaction->method('updateOrder')
             ->willReturn(true);
        $this->stubPlaceToPay->method('query')
             ->willReturn($response);
        
        $placeToPay = new EstrategyPlaceToPay($this->stubPlaceToPay, $this->stubTransaction);
        $resposneReturn = $placeToPay->getInfoPay($this->stubTransaction);

        $this->assertEquals($resposneReturn->success, true);
        $this->assertEquals($resposneReturn->data['status'], $response->status()->status());
        $this->assertEquals($resposneReturn->data['message'], $response->status()->message());
    }

    /**
     * Retorna el arreglo con la respuesta de una creacion de pago.
     *
     * @param string|null $status Es el estado para asignar a la respuesta.
     * @param string|null $message Es el mensaje para asignar a la respuesta.
     * @return  RedirectResponse Objeto con la respuesta.
     */
    public function returnRedirectResponse($status = null, $message = null)
    {
        return new RedirectResponse([
            "status" => [
              "status" => $status ?? "OK",
              "reason" => "401",
              "message" => $message ?? "Autenticación fallida 102",
              "date" => "2020-01-19T00:09:40-05:00",
            ],
            "requestId" => 181348,
            "processUrl" => "https://test.placetopay.com/redirection/session/181348/43d83d36aa46de5f993aafb9b3e0be48",
        ]);
    }

    /**
     * Retorna el arreglo con la respuesta de un estado.
     *
     * @param string|null $status Es el estado para asignar a la respuesta.
     * @param string|null $message Es el mensaje para asignar a la respuesta.
     * @return  RedirectInformation Objeto con la respuesta.
     */
    public function returnRedirectInformation($status = null, $message = null)
    {
        return new RedirectInformation([
            "requestId"=>22122,
            "status"=>[
                "status"=> $status ?? 'REJECTED',
                "reason"=>"?C",
                "message"=> $message ?? 'La petición ha sido cancelada por el usuario',
                "date"=>"2020-01-16T17:52:51-05:00",
              
            ],
            "request"=>[
                "locale"=>"es_CO",
                "buyer"=>[
                    "name"=>"John Fredy Velasco Bareño",
                    "email"=>"jovel882@gmail.com",
                    "mobile"=>"+573202919054",
                
                ],
                "payment"=>[
                "reference"=>"PTP_511579215161",
                "description"=>"Compra de (52) Item 882",
                "amount"=>[
                    "taxes"=>[
                    [
                        "kind"=>"iva",
                        "amount"=>0,
                        "base"=>0,
                        
                    ],
                    
                    ],
                    "currency"=>"COP",
                    "total"=>"260000.00",
                    
                ],
                "allowPartial"=>false,
                "items"=>[
                    [
                    "name"=>"Item 882",
                    "price"=>5000,
                    
                    ],
                    
                ],
                "subscribe"=>false,
                
                ],
                "returnUrl"=>"http://evertec.test/transactions/receive/place_to_pay/3f8c34f6-967d-4eb2-a9b9-22ca32f9e838",
                "ipAddress"=>"192.168.0.6",
                "userAgent"=>"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36",
                "expiration"=>"2020-01-16T18:52:41-05:00",
                "captureAddress"=>false,
                "skipResult"=>false,
                "noBuyerFill"=>false,
            ],
            
        ]);
    }
}

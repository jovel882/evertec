<?php

namespace Tests\Unit\Strategies\Pay;

use Tests\TestCase;
use Dnetix\Redirection\PlacetoPay as LibPlacetoPay;
use App\Models\Order;
use App\Models\Transaction;
use App\User;
use App\Strategies\Pay\PlaceToPay as EstrategyPlaceToPay;
use Dnetix\Redirection\Message\RedirectResponse;

class PlaceToPay extends TestCase
{   
    /**
     * A basic unit test example.
     *
     * @test
     * @return void
     */
    public function create_payment_and_receive_payment_request_creation_error()
    {
        $response = new RedirectResponse([
            "status" => [
              "status" => "FAILED",
              "reason" => "401",
              "message" => "Autenticación fallida 102",
              "date" => "2020-01-19T00:09:40-05:00",
            ],            
        ]);
        $stubPlaceToPay = $this->createMock(LibPlaceToPay::class);
        $stubPlaceToPay->method('request')
             ->willReturn($response);
        $stubOrder = $this->createMock(Order::class);
        $stubUser = $this->createMock(User::class);
        $stubTransaction = $this->createMock(Transaction::class);
        $this->actingAs($stubUser);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Se genero un error al crear la transaccion en placetopay (".$response->status()->message().").");
        
        $placeToPay = new EstrategyPlaceToPay($stubPlaceToPay,$stubTransaction);
        $placeToPay->createPay($stubOrder);
    }

    /**
     * A basic unit test example.
     *
     * @test
     * @return void
     */
    public function create_payment_and_receive_transaction_creation_error()
    {
        $response = new RedirectResponse([
            "status" => [
              "status" => "OK",
              "reason" => "PC",
              "message" => "La petición se ha procesado correctamente",
              "date" => "2019-03-04T16:50:02-05:00",
            ],
            "requestId" => 181348,
            "processUrl" => "https://test.placetopay.com/redirection/session/181348/43d83d36aa46de5f993aafb9b3e0be48",
        ]);
        $stubTransaction = $this->createMock(Transaction::class);
        $stubTransaction->method('store')
            ->willReturn(false);        
        $stubPlaceToPay = $this->createMock(LibPlaceToPay::class);
        $stubPlaceToPay->method('request')
             ->willReturn($response);
        $stubOrder = $this->createMock(Order::class);
        $stubUser = $this->createMock(User::class);
        $this->actingAs($stubUser);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Se genero un error al almacenar la transaccion.');
        
        $placeToPay = new EstrategyPlaceToPay($stubPlaceToPay,$stubTransaction);
        $placeToPay->createPay($stubOrder);
    }

    /**
     * A basic unit test example.
     *
     * @test
     * @return void
     */
    public function create_payment_and_receive_transaction_status_creation_error()
    {
        $response = new RedirectResponse([
            "status" => [
              "status" => "OK",
              "reason" => "PC",
              "message" => "La petición se ha procesado correctamente",
              "date" => "2019-03-04T16:50:02-05:00",
            ],
            "requestId" => 181348,
            "processUrl" => "https://test.placetopay.com/redirection/session/181348/43d83d36aa46de5f993aafb9b3e0be48",
        ]);
        $stubTransaction = $this->createMock(Transaction::class);
        $stubTransaction->method('store')
            ->will($this->returnSelf());
        $stubTransaction->method('attachStates')
            ->willReturn(false);
        $stubPlaceToPay = $this->createMock(LibPlaceToPay::class);
        $stubPlaceToPay->method('request')
             ->willReturn($response);
        $stubOrder = $this->createMock(Order::class);
        $stubUser = $this->createMock(User::class);
        $this->actingAs($stubUser);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Se genero un error al almacenar el estado de la transaccion.');
        
        $placeToPay = new EstrategyPlaceToPay($stubPlaceToPay,$stubTransaction);
        $placeToPay->createPay($stubOrder);
    } 
    /**
     * A basic unit test example.
     *
     * @test
     * @return void
     */
    public function create_payment_and_receive_payment_url()
    {
        $response = new RedirectResponse([
            "status" => [
              "status" => "OK",
              "reason" => "PC",
              "message" => "La petición se ha procesado correctamente",
              "date" => "2019-03-04T16:50:02-05:00",
            ],
            "requestId" => 181348,
            "processUrl" => "https://test.placetopay.com/redirection/session/181348/43d83d36aa46de5f993aafb9b3e0be48",
        ]);
        $stubTransaction = $this->createMock(Transaction::class);
        $stubTransaction->method('store')
            ->will($this->returnSelf());
        $stubTransaction->method('attachStates')
            ->willReturn(true);
        $stubPlaceToPay = $this->createMock(LibPlaceToPay::class);
        $stubPlaceToPay->method('request')
             ->willReturn($response);
        $stubOrder = $this->createMock(Order::class);
        $stubUser = $this->createMock(User::class);
        $this->actingAs($stubUser);
        
        $placeToPay = new EstrategyPlaceToPay($stubPlaceToPay,$stubTransaction);
        $resposneReturn = $placeToPay->createPay($stubOrder);

        $this->assertEquals($resposneReturn, $response);
    } 
}

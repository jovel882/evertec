<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Strategies\Pay\Context;

class TransactionController extends Controller
{
    /**
     * Modelo de transaccion.
     *
     * @var Transaction
     */    
    protected $transaction;
    /**
     * Constructor de la clase.
     *
     * @param Transaction $transaction Modelo transaccion.
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }    

    /**
     * Recibe el informe de un pago.
     *
     * @return \Illuminate\Http\Response
     */
    public function receive($gateway, $uuid, Request $request)
    {        
        $transaction = $this->transaction->getByUuid($uuid);
        if ($transaction) {
            $response = $this->updateStatus($transaction);
            $return = redirect()
                ->route("orders.show", ['order' => $transaction->order->id])
                ->withInput();
            if ($response['success'] == false) {
                return $return->withErrors($response['data']);
            }            
            return $return->with('update', $response['data']);
        } else {
            abort(404);
        }
    }
    /**
     * Recibe el informe de un pago.
     *
     * @return \Illuminate\Http\Response
     */
    public static function updateStatus(Transaction $transaction)
    {
        $strategy = Context::create($transaction->gateway);
        if (!$strategy) {
            $errors = new \Illuminate\Support\MessageBag();
            $errors->add('msg_0', "El metodo de pago no esta soportado.");
            return [
                'success' => false,
                'data' => $errors,
            ];
        }
        $response = $strategy->getInfoPay($transaction);
        if (! $response->success) {
            $errors = new \Illuminate\Support\MessageBag();
            $errors->add('msg_0', "Se genero un error al actualizar la transacion.");
            $errors->add('msg_1', $response->exception->getMessage());
            return [
                'success' => false,
                'data' => $errors,
            ];
        }
        return [
            'success' => true,
            'data' => $response->data,
        ];
    }
}

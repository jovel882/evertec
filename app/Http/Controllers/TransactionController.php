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
            $strategy = Context::create('place_to_pay');
            if (!$strategy) {
                $errors = new \Illuminate\Support\MessageBag();
                $errors->add('msg_0', "El metodo de pago no esta soportado.");
                return redirect()->route("orders.show", ['order' => $transaction->order->id])->withInput()->withErrors($errors);
            }
            $response = $strategy->getInfoPay($transaction);
            if (! $response->success) {
                $errors = new \Illuminate\Support\MessageBag();
                $errors->add('msg_0', "Se genero un error al actualizar la transacion.");
                $errors->add('msg_1', $response->exception->getMessage());
                return redirect()->route("orders.show", ['order' => $transaction->order->id])->withInput()->withErrors($errors);
            }
            return redirect()->route("orders.show", ['order' => $transaction->order->id])->with('update', $response->data);
        } else {
            abort(404);
        }
    }
}

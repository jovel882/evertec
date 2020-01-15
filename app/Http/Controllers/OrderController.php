<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Requests\StoreOrder;

class OrderController extends Controller
{
    /**
     * Modelo de orden.
     *
     * @var Order
     */    
    protected $order;
    /**
     * Constructor de la clase.
     *
     * @param Order $order Modelo Order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order=$order;
    }    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $viewAny = \Gate::allows('viewAny', Order::class);
        $orders = $this->order->getAll($viewAny);
        return view("web.orders.list",compact("orders","viewAny"));
    }

    /**
     * Almacena una nueva orden.
     *
     * @param  StoreOrder  $request Request con la data y from request para validacion.
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrder $request)
    {
        $request->merge([
            'total' => $request->quantity * config('config.product_price'),
            'status' => 'CREATED',
            'user_id' => auth()->user()->id,
        ]);
        if ($order = $this->order->store($request->all())) {
            return redirect()->route("orders.show", ['order' => $order->id]);
        } else{
            $errors = new \Illuminate\Support\MessageBag();
            $errors->add('msg_0', "Se genero un error almacenando la orden.");
            return back()->withInput()->withErrors($errors);
        }
    }

    /**
     * Muestra el detalle de una orden.
     *
     * @param  int  $idOrder Id de la orden.
     * @return \Illuminate\Http\Response
     */
    public function show($idOrder)
    {
        $viewAny = \Gate::allows('viewAny', Order::class);
        $order = $this->order->getById($idOrder, $viewAny);
        if ($order) {
            if (\Gate::allows('view',$order)) {            
                return view("web.orders.view",compact("order","viewAny"));
            } else {
                abort(403);
            }
        } else {
            abort(404);
        }
    }

    /**
     * Inicial un pago.
     *
     * @param  Order  Modelo para pagar.
     * @return \Illuminate\Http\Response
     */
    public function pay(Order $order)
    {                
        if (\Gate::allows('pay',$order)) {            
            if ($order->status == 'CREATED') {
                $transaction = $order->getLastTransaction();
                if (!$transaction || ($transaction->current_status != "PENDING" && $transaction->current_status != "CREATED"))  {
                    dd("Crea una nueva");
                } else{
                    if ($transaction->current_status == "CREATED") {
                        return redirect($transaction->url??"");
                    } else{
                        return redirect()->route("orders.show", ['order' => $order->id]);    
                    }
                }                
            } else {
                return redirect()->route("orders.show", ['order' => $order->id]);    
            }
        } else {
            abort(403);
        }        
    }
}

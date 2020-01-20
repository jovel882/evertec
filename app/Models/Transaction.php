<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];

    /**
    * Relacion con la orden.
    *
    * @return Relacion.
    */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
    * Relacion con los estado.
    *
    * @return Relacion.
    */
    public function transaction_states()
    {
        return $this->hasMany('App\Models\TransactionState');
    }

    /**
     * Almacena un nuevo registro.
     *
     * @param  array $data Datos para almacenar la transaccion.
     * @return Transaction|false Modelo con la transaccion nueva o un estado false si hay algun error.
     */
    public function store($data)
    {
        try {
            return self::create($data);
        } catch (\Illuminate\Database\QueryException $exception) {
            return false;
        }
    }

    /**
     * Adjunta estados a las transacciones.
     *
     * @param  array $data Datos para almacenar la transaccion.
     * @return Transaction|false Modelo con la transaccion nueva o un estado false si hay algun error.
     */
    public function attachStates($states)
    {
        try {
            return $this->transaction_states()->createMany($states);
        } catch (\Illuminate\Database\QueryException $exception) {
            return false;
        }
    }

    /**
     * Obtiene una transaccion por el uuid requerido.
     *
     * @param string $uuid Uuid de la transaccion a buscar.
     * @return Transaction Modelo.
     */
    public function getByUuid($uuid)
    {
        return $this->with(
            [
                "transaction_states" => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'order'
            ]
        )->where("uuid", $uuid)->first();
    }

    /**
     * Actualiza una transaccion.
     *
     * @param  array $data Datos para actualizar la transaccion.
     * @return Transaction|false Modelo con la transaccion.
     */
    public function edit($data)
    {
        try {
            return $this->fill($data)->save();
        } catch (\Illuminate\Database\QueryException $exception) {
            return false;
        }
    }
    
    /**
     * Actualiza la orden de una transaccion.
     *
     * @param  array $data Datos para actualizar la orden de la transaccion.
     * @return Order|false Modelo con la transaccion.
     */
    public function updateOrder($data)
    {
        try {
            return $this->order->fill($data)->save();
        } catch (\Illuminate\Database\QueryException $exception) {
            return false;
        }
    }

    /**
     * Obtiene todas las transacciones por estado.
     *
     * @param array $status Estados de las transacciones a buscar.
     * @return Collection Coleccion con los Modelos.
     */
    public static function getByStatus(array $status)
    {
        return self::with(
            [
                "transaction_states" => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'order'
            ]
        )->whereIn('current_status', $status)
            ->whereNotNull('requestId')
            ->get();
    }
}

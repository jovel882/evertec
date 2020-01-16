<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Estados posibles de las ordenes.
    |--------------------------------------------------------------------------
    |
    | Especifica los estados posibles de las ordenes.
    | CREATED = La orden esta creada.
    | PAYED = La orden esta pagada.
    | REJECTED = La orden esta rechazada por tiempo.
    |
    */

    'order_status' => [
        "CREATED",
        "PAYED",
        "REJECTED",
    ],
    /*
    |--------------------------------------------------------------------------
    | Precio del producto.
    |--------------------------------------------------------------------------
    |
    | Especifica el valor de una unidad del producto.
    |
    */

    'product_price' => env(
        'PRODUCT_PRICE',
        5000
    ),
    /*
    |--------------------------------------------------------------------------
    | Nombre del producto.
    |--------------------------------------------------------------------------
    |
    | Especifica el nombre del producto.
    |
    */

    'product_name' => env(
        'PRODUCT_NAME',
        'Item 882'
    ),
    
    /*
    |--------------------------------------------------------------------------
    | Estados posibles de las transacciones.
    |--------------------------------------------------------------------------
    |
    | Especifica los estados posibles de las transacciones.
    | CREATED = La transaccion esta creada.
    | PAYED = La transaccion esta pagada.
    | PENDING = La transaccion esta pendiente por la entidad.
    | REJECTED = La transaccion esta rechazada.
    | REFUNDED = Reintegro de una transacción por solicitud de un tarjetahabiente al comercio.
    |
    */

    'transaction_status' => [
        "CREATED",
        "PAYED",
        "PENDING",
        "REJECTED",
        "REFUNDED",
    ],

    /*
    |--------------------------------------------------------------------------
    | Pasarelas de pago disponibles.
    |--------------------------------------------------------------------------
    |
    | Especifica las pasarelas de pago disponibles.
    | place_to_pay = Place to pay.
    | john_test = Prueba.
    |
    */

    'gateways' => [
        "place_to_pay",
        "john_test",
    ],

    /*
    |--------------------------------------------------------------------------
    | Minutos para expirar la transaccion en placetopay.
    |--------------------------------------------------------------------------
    |
    | Especifica la cantidad de minutos para expirar la transaccion.
    |
    */

    'expired_minutes_PTP' => env(
        'EXPIRED_MINUTES_PTP',
        60
    ),

    /*
    |--------------------------------------------------------------------------
    | Minutos para ejecutar la validacion de estado de los pagos.
    |--------------------------------------------------------------------------
    |
    | Especifica cada cuantos minutos se ejecuta la validacion de estado de los pagos.
    |
    */

    'minutes_verify_pay' => env(
        'MINUTES_VERIFY_PAY',
        5
    ),
];

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

    'order_status' => env(
        'ORDER_STATUS',
        [
            "CREATED",
            "PAYED",
            "REJECTED",
        ]
    ),
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
    | EXPIRED = La transaccion esta expirada. 
    |
    */

    'transaction_status' => env(
        'TRANSACTION_STATUS',
        [
            "CREATED",
            "PAYED",
            "PENDING",
            "REJECTED",
            "EXPIRED",
        ]
    ),
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
];

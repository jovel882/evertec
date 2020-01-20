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
    | Especifica el valor de una unidad del producto, entero valido.
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
    | Especifica la cantidad de minutos para expirar la transaccion, entero valido.
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
    | Especifica cada cuantos minutos se ejecuta la validacion de estado de los pagos, no debe sobrepasar los 60.
    |
    */

    'minutes_verify_pay' => env(
        'MINUTES_VERIFY_PAY',
        5
    ),

    /*
    |--------------------------------------------------------------------------
    | Dias para expirar las ordenes.
    |--------------------------------------------------------------------------
    |
    | Especifica la cantidad de dias para expirar la orden, entero valido.
    |
    */

    'expired_days_order' => env(
        'EXPIRED_DAYS_ORDER',
        1
    ),

    /*
    |--------------------------------------------------------------------------
    | Hora para expirar las ordenes.
    |--------------------------------------------------------------------------
    |
    | Especifica la hora del dia en la que se ejecuta la expiracion de ordenes debe estar en formato de hora y minutos ejemplo a las 7 de la noche seria 19:00, y a las 7 de la mañana seria 07:00 .
    |
    */

    'time_expired_orders' => env(
        'TIME_EXPIRED_ORDERS',
        '01:01'
    ),
];

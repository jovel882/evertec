<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    $quantity=$faker->numberBetween(1, 100);
    return [
        "user_id" => \App\User::inRandomOrder()->first()->id,
        "status" => $faker->randomElement(config('config.order_status')),
        "quantity" => $quantity,
        "total" => $quantity * config('config.product_price'),
    ];
});

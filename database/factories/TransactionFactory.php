<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        "order_id" => \App\Models\Order::inRandomOrder()->first()->id,
        "uuid" => $faker->uuid,
        "current_status" => $faker->randomElement(config('config.transaction_status')),
        "gateway" => $faker->randomElement(config('config.gateways')),
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TransactionState;
use Faker\Generator as Faker;

$factory->define(TransactionState::class, function (Faker $faker) {
    return [
        "transaction_id" => \App\Models\Transaction::inRandomOrder()->first()->id,
        "status" => $faker->randomElement(config('config.transaction_status')),
        "data" => "{}",
    ];
});

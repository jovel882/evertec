<?php

use Illuminate\Database\Seeder;
use App\Models\TransactionState;

class TransactionStatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(TransactionState::class, 50)->create();
    }
}

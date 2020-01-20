<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Http\Controllers\TransactionController;

class ValidatePayStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:refresh_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza contra las pasarelas las transacciones que no estan en estados finales.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Transaction::getByStatus([
            'CREATED',
            'PENDING',
        ])->each(function ($transaction, $key) {
            TransactionController::updateStatus($transaction);
        });
    }
}

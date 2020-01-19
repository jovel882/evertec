<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_states', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaction_id')->comment("Es el id de la transaccion.");
            $table->string('status')->comment("Los estados posibles son PAYED, PENDING, REJECTED, EXPIRED.");
            $table->mediumText('data')->comment("Data completa del response.");
            $table->timestamps();
            $table->softDeletes();
            $table->index('transaction_id');
            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        if (app()->env != "testing") {
            \DB::statement('ALTER TABLE `transaction_states` comment "Es la tabla donde se almacenan los estados de las transacciones." ');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        Schema::dropIfExists('transaction_states');
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }
}

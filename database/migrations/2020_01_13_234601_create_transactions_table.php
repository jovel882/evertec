<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->comment("Es el id del pedido.");
            $table->uuid('uuid')->comment("Es identificador de la transaccion en el sistema.")->unique();
            $table->string('current_status')->comment("Es el estado actual. Los estados posibles son CREATED, PAYED, PENDING, REJECTED, EXPIRED.");
            $table->string('reference')->nullable(true)->comment("Es la referencia unica enviada a la pasarela.");
            $table->string('url')->nullable(true)->comment("Url de redireccion para pago.");
            $table->string('requestId')->nullable(true)->comment("Es la referencia unica dentro de la pasarela.");
            $table->timestamps();
            $table->softDeletes();
            $table->index('order_id');
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        if (app()->env != "testing") {
            \DB::statement('ALTER TABLE `transactions` comment "Es la tabla donde se almacenan las transacciones de las ordenes." ');
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
        Schema::dropIfExists('transactions');
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }
}

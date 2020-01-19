<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment("Es el id del usuario que reliza el pedido.");
            $table->string('status', 20)->comment("Los estados posibles son CREATED, PAYED, REJECTED.");
            $table->unsignedSmallInteger("quantity")->comment("Cantidad del producto. Maximo 32767");
            $table->decimal("total", 20, 2)->comment("Valor total de la orden. Maximo 17 digitos con 2 decimas de precision.");
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        if (app()->env != "testing") {
            \DB::statement('ALTER TABLE `orders` comment "Es la tabla donde se almacenan las ordenes de pedido por usuario." ');
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
        Schema::dropIfExists('orders');
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }
}

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
            // History of product orders
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');          // user that is adding items to cart and thus creating an order
            // 2€ cost for each order
            $table->unsignedDouble('bill');             // total order value
            $table->boolean('status')->default(1);      // created(1), ordered(0)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

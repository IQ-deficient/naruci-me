<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            // Food servings (portions)
            $table->bigIncrements('id');
            $table->string('name', 64);
            $table->text('description');
            $table->unsignedDouble('price')->default(0);
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->unsignedBigInteger('user_id');          // administrator user that creates entries
            $table->boolean('status')->default(1);      // // available(1), unavailable(0)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}

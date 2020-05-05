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
            $table->bigIncrements('id');
            $table->string('internal_id', 50)->nullable()->unique();
            $table->string('name');
            $table->integer('alert_level')->nullable();
            $table->double('sale_price', 8, 2)->nullable();
            $table->double('cost', 8, 2)->nullable();
            $table->string('unit')->nullable();
            $table->boolean('enabled');
            $table->timestamps();
            $table->unSignedBigInteger('user_id');
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
        Schema::dropIfExists('products');
    }
}

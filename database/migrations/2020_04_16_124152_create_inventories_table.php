<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unSignedBigInteger('product_id');
            $table->unSignedBigInteger('storage_id');
            $table->integer('quantity');
            $table->boolean('alert')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('storage_id')->references('id')->on('storages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventories');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unSignedBigInteger('transaction_id');
            $table->unSignedBigInteger('product_id');
            $table->unSignedBigInteger('old_storage_id')->nullable();
            $table->unSignedBigInteger('storage_id');
            $table->integer('quantity');
            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('storage_id')->references('id')->on('storages');
            $table->foreign('old_storage_id')->references('id')->on('storages');
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
        Schema::dropIfExists('transaction_details');
    }
}

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
            $table->string('dr_number')->nullable();
            $table->string('transaction_type');
            $table->tinyInteger('status');
            $table->unSignedBigInteger('company_id')->nullable();
            $table->unSignedBigInteger('processed_by')->nullable();
            $table->unSignedBigInteger('validated_by')->nullable();
            $table->unSignedBigInteger('warehouse_id')->nullable();
            $table->text('remarks')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('processed_by')->references('id')->on('users');
            $table->foreign('validated_by')->references('id')->on('users');
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
        Schema::dropIfExists('transactions');
    }
}

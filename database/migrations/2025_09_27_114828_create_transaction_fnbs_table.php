<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionFnbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_fnbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('fnb_id');
            $table->integer('qty');
            $table->integer('harga_jual');
            $table->timestamps();

            $table->foreign('transaction_id')->references('id_transaksi')->on('transactions')->onDelete('cascade');
            $table->foreign('fnb_id')->references('id')->on('fnbs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_fnbs');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fnb_id');
            $table->enum('type', ['in', 'out']);
            $table->integer('qty');
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('stock_mutations');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHourlyPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hourly_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('playstation_id');
            $table->integer('hour'); // Durasi jam (misal: 1, 2, 3)
            $table->integer('price'); // Harga untuk durasi tsb
            $table->timestamps();

            $table->foreign('playstation_id')->references('id')->on('playstations')->onDelete('cascade');
            $table->unique(['playstation_id', 'hour']); // Pastikan hanya ada satu harga per jam
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hourly_prices');
    }
}

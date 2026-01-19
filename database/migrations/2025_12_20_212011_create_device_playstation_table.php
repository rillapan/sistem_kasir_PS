<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicePlaystationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_playstation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('playstation_id');
            $table->timestamps();
            
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('playstation_id')->references('id')->on('playstations')->onDelete('cascade');
            
            $table->unique(['device_id', 'playstation_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_playstation');
    }
}

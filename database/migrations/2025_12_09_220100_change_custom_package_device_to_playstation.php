<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCustomPackageDeviceToPlaystation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the old table and recreate with playstation relationship
        Schema::dropIfExists('custom_package_device');
        
        Schema::create('custom_package_playstation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('playstation_id')->constrained()->onDelete('cascade');
            $table->integer('lama_main')->comment('Duration in minutes');
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
        Schema::dropIfExists('custom_package_playstation');
        
        // Recreate the original table
        Schema::create('custom_package_device', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->integer('lama_main')->comment('Duration in minutes');
            $table->timestamps();
        });
    }
}

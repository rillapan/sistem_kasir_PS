<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomPackagePriceGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_package_price_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_package_id')->constrained('custom_packages')->onDelete('cascade');
            $table->foreignId('price_group_id')->constrained('price_groups')->onDelete('cascade');
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
        Schema::dropIfExists('custom_package_price_group');
    }
}

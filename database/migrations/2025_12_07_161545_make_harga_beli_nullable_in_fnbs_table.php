<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeHargaBeliNullableInFnbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fnbs', function (Blueprint $table) {
            $table->integer('harga_beli')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fnbs', function (Blueprint $table) {
            $table->integer('harga_beli')->nullable(false)->change();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHargaBeliToTransactionFnbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_fnbs', function (Blueprint $table) {
            $table->integer('harga_beli')->default(0)->after('harga_jual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_fnbs', function (Blueprint $table) {
            $table->dropColumn('harga_beli');
        });
    }
}

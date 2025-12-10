<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTipeTransaksiEnumValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN tipe_transaksi ENUM('prepaid', 'postpaid', 'custom_package') DEFAULT 'prepaid'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN tipe_transaksi ENUM('prepaid', 'postpaid', 'custom_paket') DEFAULT 'prepaid'");
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTransactionFnbForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_fnbs', function (Blueprint $table) {
            // Hapus foreign key yang bermasalah
            $table->dropForeign(['transaction_id']);
            
            // Ubah tipe kolom transaction_id agar sesuai dengan id_transaksi di tabel transactions
            $table->unsignedBigInteger('transaction_id')->change();
            
            // Tambahkan foreign key yang benar
            $table->foreign('transaction_id')
                  ->references('id_transaksi')
                  ->on('transactions')
                  ->onDelete('cascade');
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
            // Kembalikan ke foreign key semula
            $table->dropForeign(['transaction_id']);
            
            // Kembalikan ke foreign key yang salah (untuk rollback)
            $table->foreign('transaction_id')
                  ->references('id')
                  ->on('transactions')
                  ->onDelete('cascade');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyIdTransaksiInTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('transactions', function (Blueprint $table) {
        //     // To be safe, let's drop foreign key constraints if they exist
        //     // and then re-add them after the column is renamed.
        //     // It's good practice although in this specific case there might not be any.

        //     // Rename the column
        //     $table->renameColumn('id_transaksi', 'id');
        // });

        // Now, modify the column to be auto-incrementing
        // In SQLite, this is more complex. For other DBs, this would be more direct.
        // Given the context, a fresh auto-incrementing primary key is better.
        // However, altering a column to be auto-incrementing is not straightforward in all DBs.
        // A safer and more universal approach is to add a new auto-incrementing column,
        // migrate data, and then drop the old column. But for this project,
        // let's assume we can directly modify it.
        // COMMENTED OUT TO AVOID DUPLICATE COLUMN ERROR
        // Schema::table('transactions', function (Blueprint $table) {
        //     $table->bigIncrements('id')->first();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // This reversal is tricky. For simplicity, we'll just rename back.
            // The auto-increment property change might be irreversible depending on the DB.
            $table->renameColumn('id', 'id_transaksi');
        });
    }
}

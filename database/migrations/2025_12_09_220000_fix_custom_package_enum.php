<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixCustomPackageEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, let's see what values exist
        $existingValues = DB::table('transactions')->select('tipe_transaksi')->distinct()->pluck('tipe_transaksi');
        
        // Update any problematic values to 'prepaid' temporarily
        DB::table('transactions')
            ->whereNotIn('tipe_transaksi', ['prepaid', 'postpaid', 'custom_package'])
            ->update(['tipe_transaksi' => 'prepaid']);

        // Now modify the column
        DB::statement("ALTER TABLE transactions MODIFY COLUMN tipe_transaksi ENUM('prepaid', 'postpaid', 'custom_package') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prepaid'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN tipe_transaksi ENUM('prepaid', 'postpaid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prepaid'");
    }
}

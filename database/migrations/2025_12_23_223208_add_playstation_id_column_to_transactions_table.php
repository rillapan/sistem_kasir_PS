<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlaystationIdColumnToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('transactions', 'playstation_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('playstation_id')->nullable()->after('device_id');
                $table->foreign('playstation_id')->references('id')->on('playstations')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['playstation_id']);
            $table->dropColumn('playstation_id');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceGroupToCustomPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('price_group_id')->nullable()->after('deskripsi');
            $table->foreign('price_group_id')->references('id')->on('price_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_packages', function (Blueprint $table) {
            $table->dropForeign(['price_group_id']);
            $table->dropColumn('price_group_id');
        });
    }
}

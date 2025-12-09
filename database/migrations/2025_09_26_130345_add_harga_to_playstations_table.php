<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddHargaToPlaystationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('playstations', function (Blueprint $table) {
            $table->string('harga')->nullable();
        });

        // Migrate data from harga_normal to harga
        DB::statement('UPDATE playstations SET harga = harga_normal WHERE harga_normal IS NOT NULL');

        Schema::table('playstations', function (Blueprint $table) {
            $table->dropColumn(['harga_normal', 'harga_member']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('playstations', function (Blueprint $table) {
            $table->string('harga_normal')->nullable();
            $table->string('harga_member')->nullable();
        });

        // Reverse data migration
        DB::statement('UPDATE playstations SET harga_normal = harga, harga_member = harga WHERE harga IS NOT NULL');

        Schema::table('playstations', function (Blueprint $table) {
            $table->dropColumn('harga');
        });
    }
}

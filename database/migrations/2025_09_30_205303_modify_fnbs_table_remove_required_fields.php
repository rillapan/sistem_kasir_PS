<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFnbsTableRemoveRequiredFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fnbs', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'satuan', 'gambar']);
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
            $table->string('kategori');
            $table->string('satuan');
            $table->string('gambar')->nullable();
        });
    }
}

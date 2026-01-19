<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateUsersTableForRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Ubah kolom status menjadi role dengan enum yang jelas
            $table->enum('role', ['admin', 'owner', 'kasir'])->default('kasir')->after('password');
            
            // Tambah kolom shift untuk kasir (nullable karena owner dan admin tidak punya shift)
            $table->string('shift')->nullable()->after('role');
        });

        // Migrate existing status data to role
        DB::statement("UPDATE users SET role = 'admin' WHERE status = 'admin'");

        Schema::table('users', function (Blueprint $table) {
            // Drop kolom status lama jika ada
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'shift']);
            $table->string('status')->after('password');
        });
    }
}

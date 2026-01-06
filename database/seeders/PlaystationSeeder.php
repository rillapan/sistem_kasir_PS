<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class PlaystationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('playstations')->insertOrIgnore([
            [
                'id' => 1,
                'nama' => 'PS4',
                'image' => 'post-images/ps4.jpg',
                'harga' => 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nama' => 'PS5',
                'image' => 'post-images/ps5.jpg',
                'harga' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nama' => 'BILLIARD pagi',
                'image' => 'post-images/billiard.jpg',
                'harga' => 25000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'nama' => 'BILLIARD sore',
                'image' => 'post-images/billiard.jpg',
                'harga' => 30000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more if needed for other devices
        ]);
    }
}

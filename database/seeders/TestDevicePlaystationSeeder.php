<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\Playstation;
use Illuminate\Support\Facades\DB;

class TestDevicePlaystationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing relationships for testing
        DB::table('device_playstation')->delete();
        
        // Create sample PlayStation types with different prices
        $playstations = [
            ['nama' => 'BILLIARD PAGI', 'harga' => 25000],
            ['nama' => 'BILLIARD SORE', 'harga' => 35000],
            ['nama' => 'BILLIARD PAKET', 'harga' => 45000],
            ['nama' => 'PS5 STANDAR', 'harga' => 40000],
            ['nama' => 'PS5 VIP', 'harga' => 60000],
        ];
        
        $createdPlaystations = [];
        foreach ($playstations as $ps) {
            $playstation = Playstation::create($ps);
            $createdPlaystations[] = $playstation;
        }
        
        // Get or create sample devices
        $devices = [
            ['nama' => 'MEJA 1', 'status' => 'Tersedia', 'playstation_id' => 1],
            ['nama' => 'MEJA 2', 'status' => 'Tersedia', 'playstation_id' => 1],
            ['nama' => 'MEJA 3', 'status' => 'Tersedia', 'playstation_id' => 1],
            ['nama' => 'PS5-01', 'status' => 'Tersedia', 'playstation_id' => 4],
        ];
        
        $createdDevices = [];
        foreach ($devices as $deviceData) {
            $device = Device::firstOrCreate(['nama' => $deviceData['nama']], $deviceData);
            $createdDevices[] = $device;
        }
        
        // Create relationships: MEJA devices have multiple billiard types
        // MEJA 1 - has all 3 billiard types
        $createdDevices[0]->playstations()->attach([
            $createdPlaystations[0]->id, // BILLIARD PAGI
            $createdPlaystations[1]->id, // BILLIARD SORE
            $createdPlaystations[2]->id, // BILLIARD PAKET
        ]);
        
        // MEJA 2 - has 2 billiard types
        $createdDevices[1]->playstations()->attach([
            $createdPlaystations[0]->id, // BILLIARD PAGI
            $createdPlaystations[1]->id, // BILLIARD SORE
        ]);
        
        // MEJA 3 - has 1 billiard type (for testing single type)
        $createdDevices[2]->playstations()->attach([
            $createdPlaystations[2]->id, // BILLIARD PAKET
        ]);
        
        // PS5-01 - has 2 PS5 types
        $createdDevices[3]->playstations()->attach([
            $createdPlaystations[3]->id, // PS5 STANDAR
            $createdPlaystations[4]->id, // PS5 VIP
        ]);
        
        $this->command->info('Test device-playstation relationships created successfully!');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkShift;

class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shifts = [
            [
                'nama_shift' => 'Pagi',
                'jam_mulai' => '06:00:00',
                'jam_selesai' => '14:00:00',
                'keterangan' => 'Shift pagi 06:00 - 14:00'
            ],
            [
                'nama_shift' => 'Siang',
                'jam_mulai' => '14:00:00',
                'jam_selesai' => '22:00:00',
                'keterangan' => 'Shift siang 14:00 - 22:00'
            ],
            [
                'nama_shift' => 'Malam',
                'jam_mulai' => '22:00:00',
                'jam_selesai' => '06:00:00',
                'keterangan' => 'Shift malam 22:00 - 06:00 (overnight)'
            ],
        ];

        foreach ($shifts as $shift) {
            WorkShift::create($shift);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Operator 1',
                'email' => 'operator1@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'kasir',
                'work_shift_id' => 1,
            ],
            [
                'name' => 'Operator 2',
                'email' => 'operator2@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'kasir',
                'work_shift_id' => 2,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
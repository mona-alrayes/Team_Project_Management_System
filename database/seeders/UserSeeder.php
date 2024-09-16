<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => '12345678',
            'system_role' => 'admin',
        ]);

        $mona = User::factory()->create([
            'name' => 'mona',
            'email' => 'mona@gmail.com',
            'password' => '12345678',
            'system_role' => 'user',
        ]);

        $hani = User::factory()->create([
            'name' => 'hani',
            'email' => 'hani@gmail.com',
            'password' => '12345678',
            'system_role' => 'user',
        ]);

        $ayham = User::factory()->create([
            'name' => 'ayham',
            'email' => 'ayham@gmail.com',
            'password' => '12345678',
            'system_role' => 'user',
        ]);

        $somar = User::factory()->create([
            'name' => 'somar',
            'email' => 'somar@gmail.com',
            'password' => '12345678',
            'system_role' => 'user',
        ]);

        $yosef = User::factory()->create([
            'name' => 'yosef',
            'email' => 'yosef@gmail.com',
            'password' => '12345678',
            'system_role' => 'user',
        ]);
    }
}

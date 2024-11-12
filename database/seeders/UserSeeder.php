<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'admin',
            'email'    => 'admin@example.com',
            'password' => 'password123'
        ]);

        User::create([
            'name'     => 'user',
            'email'    => 'user@example.com',
            'password' => 'password123'
        ]);
    }
}

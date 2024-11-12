<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
        ]);

        $user = User::findOrFail(1);
        $user->roles()->attach(1);

        $user = User::findOrFail(2);
        $user->roles()->attach(2);
    }
}

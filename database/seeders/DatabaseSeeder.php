<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@simaku.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Bendahara Sekolah',
            'email' => 'bendahara@simaku.com',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
        ]);

        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepsek@simaku.com',
            'password' => Hash::make('password'),
            'role' => 'kepsek',
        ]);
    }
}

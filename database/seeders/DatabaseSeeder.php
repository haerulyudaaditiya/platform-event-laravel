<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <-- Pastikan ini ada

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat Organizer
        User::create([
            'name' => 'Organizer Acara',
            'email' => 'organizer@example.com',
            'phone_number' => '081200000002',
            'password' => Hash::make('admin12345'),
            'role' => 'organizer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pengguna Biasa',
            'email' => 'user@example.com',
            'phone_number' => '081200000003',
            'password' => Hash::make('admin12345'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}

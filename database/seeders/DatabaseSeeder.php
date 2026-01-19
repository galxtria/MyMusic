<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // PENTING: Panggil SongSeeder di sini agar ikut dijalankan
        $this->call([
            UserSeeder::class,
            SongSeeder::class,
        ]);
    }
}
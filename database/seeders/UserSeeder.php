<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('semangat45'), 
            'role' => 'admin', 
        ]);

        // 2. Akun User Biasa
        User::create([
            'name' => 'nunuk',
            'email' => 'nunuk@gmail.com',
            'password' => Hash::make('nuk123'), 
            'role' => 'user',
        ]);
    }
    
}
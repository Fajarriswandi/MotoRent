<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No.1'
        ]);

        // Customer User 1
        User::create([
            'name' => 'Customer Satu',
            'email' => 'customer1@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '081111111111',
            'address' => 'Jl. Customer No.1'
        ]);

        // Customer User 2
        User::create([
            'name' => 'Customer Dua',
            'email' => 'customer2@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '082222222222',
            'address' => 'Jl. Customer No.2'
        ]);
    }
}

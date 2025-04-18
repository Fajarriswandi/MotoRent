<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'name' => 'Customer Satu',
            'email' => 'customer1@example.com',
            'password' => Hash::make('P4sMotoCust@'),
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 1',
            'photo' => null,
        ]);
    }
}

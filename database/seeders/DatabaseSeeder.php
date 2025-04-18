<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MotorbikeSeeder::class,
            CustomerSeeder::class,
            PermissionSeeder::class,
        ]);
    }

}



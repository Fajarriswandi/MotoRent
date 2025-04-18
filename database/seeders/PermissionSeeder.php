<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['module' => 'dashboard',   'name' => 'Dashboard'],
            ['module' => 'motorbikes',  'name' => 'Motorbikes'],
            ['module' => 'rentals',     'name' => 'Rentals'],
            ['module' => 'customers',   'name' => 'Customers'],
            ['module' => 'users',       'name' => 'Users'],
            ['module' => 'reports',     'name' => 'Laporan'],
        ];

        foreach ($permissions as $item) {
            Permission::updateOrCreate(
                ['module' => $item['module']],
                ['name' => $item['name']]
            );
        }
    }
}

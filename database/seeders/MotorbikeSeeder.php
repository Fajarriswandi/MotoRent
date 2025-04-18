<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Motorbike;

class MotorbikeSeeder extends Seeder
{
    public function run(): void
    {
        $motorList = [
            ['Honda', 'Vario 160', 2023, 'Hitam', 'B1234ABC'],
            ['Yamaha', 'NMAX', 2022, 'Putih', 'B5678XYZ'],
            ['Suzuki', 'Satria F150', 2021, 'Merah', 'B8910QWE'],
            ['Kawasaki', 'W175', 2022, 'Coklat', 'B1122RTY'],
            ['Honda', 'PCX 160', 2023, 'Silver', 'B3344UIO'],
            ['Yamaha', 'Aerox 155', 2022, 'Biru', 'B5566PAS'],
            ['Suzuki', 'Address Playful', 2021, 'Pink', 'B7788MNB'],
            ['Kawasaki', 'KLX 150', 2023, 'Hijau', 'B9900VBN'],
            ['Honda', 'Beat Street', 2022, 'Hitam Doff', 'B1010ZZZ'],
            ['Yamaha', 'Fazzio Hybrid', 2023, 'Cream', 'B2020FFF'],
        ];

        foreach ($motorList as [$brand, $model, $year, $color, $plate]) {
            Motorbike::create([
                'brand' => $brand,
                'model' => $model,
                'year' => $year,
                'color' => $color,
                'license_plate' => $plate,
                'rental_price_hour' => 15000,
                'rental_price_day' => 100000,
                'rental_price_week' => 600000,
                'status' => 'available',
                'image' => null,
            ]);
        }
    }
}

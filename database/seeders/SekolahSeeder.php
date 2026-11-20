<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'npsn' => '30000001',
                'nama' => 'SD Negeri Contoh 1',
                'jenjang' => 'SD',
                'status' => 'Negeri',
                'alamat' => 'Jl. Contoh No. 1',
                'kecamatan' => 'Banjarmasin Utara',
                'telepon' => null,
                'latitude' => -3.2950000,
                'longitude' => 114.5900000,
            ],
            [
                'npsn' => '30000002',
                'nama' => 'SD Swasta Contoh 2',
                'jenjang' => 'SD',
                'status' => 'Swasta',
                'alamat' => 'Jl. Contoh No. 2',
                'kecamatan' => 'Banjarmasin Selatan',
                'telepon' => null,
                'latitude' => -3.3500000,
                'longitude' => 114.5950000,
            ],
            [
                'npsn' => '30000003',
                'nama' => 'SMP Negeri Contoh 1',
                'jenjang' => 'SMP',
                'status' => 'Negeri',
                'alamat' => 'Jl. Contoh No. 3',
                'kecamatan' => 'Banjarmasin Tengah',
                'telepon' => null,
                'latitude' => -3.3250000,
                'longitude' => 114.5880000,
            ],
            [
                'npsn' => '30000004',
                'nama' => 'SMP Swasta Contoh 2',
                'jenjang' => 'SMP',
                'status' => 'Swasta',
                'alamat' => 'Jl. Contoh No. 4',
                'kecamatan' => 'Banjarmasin Barat',
                'telepon' => null,
                'latitude' => -3.3200000,
                'longitude' => 114.5700000,
            ],
            [
                'npsn' => '30000005',
                'nama' => 'SMA Negeri Contoh 1',
                'jenjang' => 'SMA',
                'status' => 'Negeri',
                'alamat' => 'Jl. Contoh No. 5',
                'kecamatan' => 'Banjarmasin Timur',
                'telepon' => null,
                'latitude' => -3.3100000,
                'longitude' => 114.6100000,
            ],
            [
                'npsn' => '30000006',
                'nama' => 'SMA Swasta Contoh 2',
                'jenjang' => 'SMA',
                'status' => 'Swasta',
                'alamat' => 'Jl. Contoh No. 6',
                'kecamatan' => 'Banjarmasin Tengah',
                'telepon' => null,
                'latitude' => -3.3300000,
                'longitude' => 114.6000000,
            ],
            [
                'npsn' => '30000007',
                'nama' => 'SMK Negeri Contoh 1',
                'jenjang' => 'SMK',
                'status' => 'Negeri',
                'alamat' => 'Jl. Contoh No. 7',
                'kecamatan' => 'Banjarmasin Selatan',
                'telepon' => null,
                'latitude' => -3.3600000,
                'longitude' => 114.6050000,
            ],
            [
                'npsn' => '30000008',
                'nama' => 'SMK Swasta Contoh 2',
                'jenjang' => 'SMK',
                'status' => 'Swasta',
                'alamat' => 'Jl. Contoh No. 8',
                'kecamatan' => 'Banjarmasin Utara',
                'telepon' => null,
                'latitude' => -3.2850000,
                'longitude' => 114.6000000,
            ],
        ];

        foreach ($data as $item) {
            Sekolah::updateOrCreate(
                ['npsn' => $item['npsn']],
                $item
            );
        }
    }
}

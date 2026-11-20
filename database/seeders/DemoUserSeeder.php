<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Administrator
        User::updateOrCreate(
            ['email' => 'admin@sig.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN->value,
                'sekolah_id' => null,
            ]
        );

        // Akun Dinas
        User::updateOrCreate(
            ['email' => 'dinas@sig.test'],
            [
                'name' => 'Petugas Dinas',
                'password' => Hash::make('password'),
                'role' => UserRole::DINAS->value,
                'sekolah_id' => null,
            ]
        );

        // Ambil sekolah pertama untuk akun operator
        $sekolah = Sekolah::first();

        if ($sekolah) {
            User::updateOrCreate(
                ['email' => 'operator@sig.test'],
                [
                    'name' => 'Operator Sekolah',
                    'password' => Hash::make('password'),
                    'role' => UserRole::SEKOLAH->value,
                    'sekolah_id' => $sekolah->id,
                ]
            );
        }
    }
}

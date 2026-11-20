<?php

use App\Http\Controllers\PetaController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilSekolahController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

// Halaman peta publik
Route::get('/', [PetaController::class, 'index'])
    ->name('peta.index');

Route::get('/data/sekolah', [PetaController::class, 'geojson'])
    ->name('peta.data');

// Menu pencarian sekolah (Praktikum 21)
Route::get('/pencarian', [PetaController::class, 'cari'])
    ->name('peta.cari');

// Menu klaster sekolah (Praktikum 22)
Route::get('/klaster', [PetaController::class, 'klaster'])
    ->name('peta.klaster');

// Menu peta panas sekolah (Praktikum 23)
Route::get('/panas', [PetaController::class, 'panas'])
    ->name('peta.panas');

// Menu wilayah sekolah (Praktikum 24)
Route::get('/wilayah', [PetaController::class, 'wilayah'])
    ->name('peta.wilayah');

Route::get('/data/wilayah', [PetaController::class, 'geojsonWilayah'])
    ->name('peta.data.wilayah');

// Menu rute sekolah (Praktikum 25)
Route::get('/rute', [PetaController::class, 'rute'])
    ->name('peta.rute');

// Menu cek lokasi (Praktikum 26)
Route::get('/cek-alamat', [PetaController::class, 'alamat'])
    ->name('peta.alamat');

// Profil sekolah publik
Route::get('/profil-sekolah/{sekolah}', [PetaController::class, 'profil'])
    ->name('peta.sekolah.profil');

// Halaman login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.process');
});

// Pengguna yang sudah login
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    // Dashboard
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    // Ubah kata sandi
    Route::get('/ubah-kata-sandi', [PasswordController::class, 'edit'])
        ->name('password.edit');

    Route::put('/ubah-kata-sandi', [PasswordController::class, 'update'])
        ->name('password.update');

    // Dinas: daftar, tambah, dan hapus sekolah
    Route::middleware('role:dinas')
        ->prefix('dinas')
        ->name('dinas.')
        ->group(function () {
            Route::resource('sekolah', SekolahController::class)
                ->only(['index', 'create', 'store', 'destroy']);
        });

    // Dinas dan Sekolah: mengubah data sekolah melalui Policy
    Route::middleware('role:dinas,sekolah')->group(function () {

        Route::get('/sekolah/{sekolah}/edit', [SekolahController::class, 'edit'])
            ->name('sekolah.edit');

        Route::put('/sekolah/{sekolah}', [SekolahController::class, 'update'])
            ->name('sekolah.update');

        // Profil sekolah (edit)
        Route::get('/sekolah/{sekolah}/profil', [ProfilSekolahController::class, 'edit'])
            ->name('sekolah.profil.edit');

        Route::put('/sekolah/{sekolah}/profil', [ProfilSekolahController::class, 'update'])
            ->name('sekolah.profil.update');
    });

    // Admin: kelola pengguna dan log aktivitas
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Log aktivitas
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])
                ->name('activity-logs.index');

            // Mengaktifkan atau menonaktifkan akun
            Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
                ->name('users.toggle-status');

            // CRUD pengguna
            Route::resource('users', UserController::class)
                ->except('show');
        });
});

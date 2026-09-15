<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Menambahkan Akun Pengguna Utama (Master Admin & Admin Operasional)
     */
    public function run(): void
    {
        // 1. Master Admin System (Non-Unit / Pusat)
        User::updateOrCreate(
            ['email' => 'masteradmin@asimat.com'],
            [
                'name' => 'Master Admin System',
                'password' => Hash::make('password123'),
                'role' => 'master_admin',
                'unit_id' => null,
                'penugasan' => 'Wewenang Penuh: Kontrol seluruh sistem, database, audit aset, dan hak akses',
                'status' => 'Aktif',
                'deskripsi' => 'Master Admin System - Kontrol Penuh Sistem SIMAT-RK',
            ]
        );

        // 2. Admin Operasional (Non-Unit / Pusat)
        User::updateOrCreate(
            ['email' => 'admin@asimat.com'],
            [
                'name' => 'Admin Operasional SIMAT',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'unit_id' => null,
                'permissions' => ['astap', 'distribusi', 'bast', 'mutasi', 'unit', 'master_data', 'users'],
                'penugasan' => 'Wewenang Operasional: Pengelolaan inventaris ASTAP, verifikasi pengadaan, distribusi & BAST',
                'status' => 'Aktif',
                'deskripsi' => 'Admin Operasional - Pengelola Inventaris & Distribusi Aset',
            ]
        );

        // 3. User Sub Master Universal
        User::updateOrCreate(
            ['email' => 'subadmin@asimat.com'],
            [
                'name' => 'User Sub Master (Universal)',
                'password' => Hash::make('password123'),
                'role' => 'sub_admin',
                'unit_id' => null,
                'penugasan' => 'Wewenang Unit: Pengajuan permohonan aset unit, pemantauan barang, & perbaikan',
                'status' => 'Aktif',
                'deskripsi' => 'User Sub Master Universal',
            ]
        );
    }
}

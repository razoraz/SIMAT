<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Menambahkan User Seeders
     */
    public function run(): void
    {
        // Master Admin
        User::updateOrCreate(
            ['email' => 'masteradmin@asimat.com'],
            [
                'name' => 'Master Admin System',
                'password' => Hash::make('password123'),
                'role' => 'master_admin',
                'deskripsi' => 'Master Admin System',
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@asimat.com'],
            [
                'name' => 'Admin Operasional',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'deskripsi' => 'Admin Operasional',
            ]
        );

        // Sub Admin
        User::updateOrCreate(
            ['email' => 'subadmin@asimat.com'],
            [
                'name' => 'User Sub Master',
                'password' => Hash::make('password123'),
                'role' => 'sub_admin',
                'deskripsi' => 'User Sub Master',
            ]
        );
    }
}

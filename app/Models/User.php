<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang boleh diisi
     */
    protected $fillable = [
        'name',
        'email',
        'nip',
        'password',
        'role',
        'unit',
        'penugasan',
        'status',
        'deskripsi',
    ];

    /**
     * Atribut yang disembunyikan
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts untuk tipe data
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Helper Methods Pengecekan Role
     */
    /**
     * Cek apakah user adalah Master Admin
     */
    public function isMasterAdmin(): bool
    {
        return $this->role === 'master_admin';
    }

    /**
     * Cek apakah user adalah Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah Sub Admin
     */
    public function isSubAdmin(): bool
    {
        return $this->role === 'sub_admin';
    }
}

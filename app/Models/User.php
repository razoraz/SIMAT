<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang boleh diisi pada tabel users
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'penugasan',
        'status',
        'deskripsi',
        'unit_id',
    ];

    /**
     * Atribut dinamis yang diambil langsung dari relasi Unit
     */
    protected $appends = [
        'unit',
        'nip',
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
     * Relasi ke Unit / Ruangan RSUD (User memiliki unit_id)
     */
    public function unitModel()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Dynamic Accessor: Mengambil Nama Unit langsung dari tabel units
     */
    public function getUnitAttribute(): ?string
    {
        if ($this->relationLoaded('unitModel')) {
            return $this->unitModel?->nama;
        }
        return $this->unitModel()->value('nama');
    }

    /**
     * Dynamic Accessor: Mengambil NIP Pejabat langsung dari tabel units
     */
    public function getNipAttribute(): ?string
    {
        if ($this->relationLoaded('unitModel')) {
            return $this->unitModel?->nip;
        }
        return $this->unitModel()->value('nip');
    }

    /**
     * Cek apakah user adalah Master Admin
     */
    public function isMasterAdmin(): bool
    {
        return $this->role === 'master_admin';
    }

    /**
     * Cek apakah user adalah Admin Operasional
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

    /**
     * Booted Hook: Sinkronisasi otomatis ke tabel units saat name / email user diubah
     */
    protected static function booted(): void
    {
        static::updated(function (User $user) {
            if ($user->unit_id) {
                $unit = Unit::find($user->unit_id);
                if ($unit) {
                    // Cek jika nama atau email berubah -> sinkronkan ke data Unit
                    if ($unit->kepala !== $user->name || ($user->email && $unit->email !== $user->email)) {
                        $unit->withoutEvents(function () use ($unit, $user) {
                            $unit->update([
                                'kepala' => $user->name,
                                'email' => $user->email,
                            ]);
                        });
                    }
                }
            }
        });
    }
}

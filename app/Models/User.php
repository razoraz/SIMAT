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
        'permissions',
        'status',
        'deskripsi',
        'unit_id',
    ];

    /**
     * Daftar Definisi Hak Akses Modul Operasional SIMAT-RK
     */
    public const AVAILABLE_PERMISSIONS = [
        'astap' => [
            'label' => 'Data ASTAP & Kode 108',
            'icon' => '📦',
            'color' => 'emerald',
            'description' => 'Kelola inventaris aset tetap, form input, barcode QR, export, dan klasifikasi kode 108'
        ],
        'distribusi' => [
            'label' => 'Distribusi ASTAP',
            'icon' => '🚚',
            'color' => 'teal',
            'description' => 'Kelola penyaluran aset ke ruangan, verifikasi penerimaan, dan status distribusi'
        ],
        'bast' => [
            'label' => 'Berita Acara (BAST)',
            'icon' => '📜',
            'color' => 'blue',
            'description' => 'Pembuatan dan penandatanganan Berita Acara Serah Terima (BAST) & cetak triwulan'
        ],
        'mutasi' => [
            'label' => 'Mutasi Aset',
            'icon' => '🔄',
            'color' => 'amber',
            'description' => 'Kelola dan persetujuan mutasi/perpindahan aset antar ruangan RSUD'
        ],
        'unit' => [
            'label' => 'Unit & Paviliun',
            'icon' => '🏥',
            'color' => 'indigo',
            'description' => 'Kelola katalog data unit, ruangan, paviliun, penanggung jawab, dan NIP'
        ],
        'master_data' => [
            'label' => 'Master SIPD (Pengadaan & Rekening)',
            'icon' => '⚙️',
            'color' => 'cyan',
            'description' => 'Kelola data master Jenis Pengadaan dan Rekening Belanja SIPD'
        ],
        'users' => [
            'label' => 'Manajemen Pengguna',
            'icon' => '👥',
            'color' => 'rose',
            'description' => 'Kelola akun pegawai, staf operasional, dan hak akses sistem'
        ],
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
            'permissions' => 'array',
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
     * Dynamic Accessor Role: Otomatis berpangkat 'admin' jika bertugas di Bagian Rumah Tangga & Inst Perbekalan
     */
    public function getRoleAttribute($value): string
    {
        $role = $value ?? 'sub_admin';
        if ($role === 'sub_admin' && str_contains(strtolower($this->unit ?? ''), 'rumah tangga')) {
            return 'admin';
        }
        return $role;
    }

    /**
     * Cek apakah user adalah Master Admin
     */
    public function isMasterAdmin(): bool
    {
        return $this->role === 'master_admin';
    }

    /**
     * Cek apakah user adalah Admin Operasional (Termasuk Bagian Rumah Tangga & Inst Perbekalan)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || str_contains(strtolower($this->unit ?? ''), 'rumah tangga');
    }

    /**
     * Cek apakah user adalah Sub Admin
     */
    public function isSubAdmin(): bool
    {
        return $this->role === 'sub_admin' && !str_contains(strtolower($this->unit ?? ''), 'rumah tangga');
    }

    /**
     * Cek Hak Akses Modul Operasional untuk User
     */
    public function canAccess(string $module): bool
    {
        // 1. Master Admin memiliki akses mutlak ke seluruh modul
        if ($this->role === 'master_admin') {
            return true;
        }

        // 2. Sub Admin bukan admin operasional
        if ($this->role === 'sub_admin') {
            return false;
        }

        // 3. Admin Operasional: cek permissions
        $permissions = $this->permissions;

        // Fallback backward compatibility jika belum di-set permissions
        if ($permissions === null || !is_array($permissions)) {
            return true;
        }

        return in_array($module, $permissions, true);
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

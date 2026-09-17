<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Unit extends Model
{
    use HasFactory, \App\Traits\TrackableSoftDelete;

    protected $table = 'units';

    protected $fillable = [
        'kode_unit',
        'nama',
        'tipe',
        'kepala',
        'nip',
        'email',
        'id_aset',
        'total_aset',
        'total_nilai',
        'is_deleted',
        'deleted_by',
        'deleted_by_id',
        'deleted_at',
    ];

    protected $casts = [
        'id_aset'    => 'array',
        'total_aset' => 'integer',
        'is_deleted' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke akun User (Sub Admin ruangan ini)
     */
    public function user()
    {
        return $this->hasOne(User::class, 'unit_id');
    }

    /**
     * Relasi ke riwayat Dokumen Distribusi & BAST yang pernah diserahkan ke unit ini
     */
    public function distribusis()
    {
        return $this->hasMany(Distribusi::class, 'unit_id');
    }

    /**
     * Generate Kode Unit otomatis format UNIT-(3 digit angka nomor urut)
     * Contoh: UNIT-001, UNIT-002, ..., UNIT-056
     */
    public static function generateNextKode(): string
    {
        $maxNum = 0;
        $allKodes = self::pluck('kode_unit');
        foreach ($allKodes as $kode) {
            if (preg_match('/^UNIT-(\d+)$/i', $kode, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $nextNum = $maxNum > 0 ? ($maxNum + 1) : (self::count() + 1);
        return 'UNIT-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Hook Boot: Otomatisasi pendaftaran & sinkronisasi akun Sub Admin
     */
    protected static function booted(): void
    {
        // 0. Saat Unit baru akan dibuat -> pastikan kode_unit selalu terisi otomatis UNIT-(3 digit)
        static::creating(function (Unit $unit) {
            if (empty($unit->kode_unit)) {
                $unit->kode_unit = self::generateNextKode();
            }
        });

        // 1. Saat Unit baru dibuat -> otomatis buat Akun Sub Admin dengan unit_id menunjuk ke unit ini
        static::created(function (Unit $unit) {
            $email = $unit->email ?: (Str::slug($unit->nama, '.') . '@rsudkoesnandi.id');
            
            User::withoutEvents(function () use ($unit, $email) {
                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $unit->kepala,
                        'role' => 'sub_admin',
                        'unit_id' => $unit->id,
                        'penugasan' => 'Sub Admin Ruangan ' . $unit->nama,
                        'status' => 'Aktif',
                        'password' => Hash::make('rsud123'),
                        'deskripsi' => 'Akun Sub Admin Otomatis dari Pendaftaran Unit ' . $unit->nama,
                    ]
                );
            });
        });

        // 2. Saat Unit diubah -> sinkronkan nama kepala dan email ke User Sub Admin terkait
        static::updated(function (Unit $unit) {
            $user = User::where('unit_id', $unit->id)->first();
            if ($user) {
                if ($user->name !== $unit->kepala || ($unit->email && $user->email !== $unit->email)) {
                    $user->withoutEvents(function () use ($user, $unit) {
                        $user->update([
                            'name' => $unit->kepala,
                            'email' => $unit->email ?: $user->email,
                            'penugasan' => 'Sub Admin Ruangan ' . $unit->nama,
                        ]);
                    });
                }
            }
        });

        // 3. Saat Unit akan dihapus permanen -> Proteksi: Blokir jika memiliki riwayat BAST Distribusi
        static::deleting(function (Unit $unit) {
            $bastCount = \App\Models\Distribusi::where('unit_id', $unit->id)->count();
            if ($bastCount > 0) {
                throw new \Exception("Penghapusan permanen ditolak: Unit \"{$unit->nama}\" memiliki {$bastCount} riwayat dokumen BAST Distribusi yang dilindungi untuk keperluan audit BPK & Inspektorat.");
            }
        });

        // 4. Saat Unit telah dihapus permanen -> hapus akun sub admin terkait
        static::deleted(function (Unit $unit) {
            User::withoutEvents(function () use ($unit) {
                User::where('unit_id', $unit->id)->delete();
            });
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
     * Relasi ke seluruh akun User pada ruangan ini
     */
    public function users()
    {
        return $this->hasMany(User::class, 'unit_id');
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
     * Ambil seluruh aset register NIBAR aktif yang terhubung ke unit ini.
     */
    public function getActiveRegisters()
    {
        // 1. Cari register ASTAP yang terhubung via unit_id atau ruang_pemegang
        $registers = \App\Models\AstapRegister::with(['astap.jenisAstap'])
            ->where('is_deleted', 0)
            ->where(function($q) {
                $q->whereNull('status')
                  ->orWhere(function($sub) {
                      $sub->whereNotIn('status', ['Mutasi Keluar OPD', 'Kemitraan', 'Dihibahkan']);
                  });
            })
            ->where(function($q) {
                $q->where('unit_id', $this->id)
                  ->orWhere(function($q2) {
                      $q2->whereNotNull('ruang_pemegang')
                         ->where('ruang_pemegang', '!=', '')
                         ->where(function($q3) {
                             $q3->where('ruang_pemegang', $this->nama)
                                ->orWhere('ruang_pemegang', 'LIKE', '%' . $this->nama . '%');
                         });
                  });
            })
            ->get();

        // 2. Ambil ASTAP yang terhubung langsung via unit_id
        $existingAstapIds = $registers->pluck('astap_id')->filter()->unique();
        $astapDirectIds = \App\Models\Astap::where('unit_id', $this->id)
            ->where('is_deleted', 0)
            ->where(function($q) {
                $q->whereNull('kondisi')
                  ->orWhere('kondisi', '!=', 'Mutasi Keluar OPD');
            })
            ->where(function($q) {
                $q->whereNull('sumber_dana')
                  ->orWhere('sumber_dana', '!=', 'kemitraan');
            })
            ->where(function($q) {
                $q->whereNull('category')
                  ->orWhere('category', '!=', 'KEMITRAAN');
            })
            ->whereDoesntHave('kemitraan', function($kq) {
                $kq->where('is_deleted', 0)->whereIn('status_konsesi', ['Aktif', 'Perpanjangan']);
            })
            ->when($existingAstapIds->isNotEmpty(), function($q) use ($existingAstapIds) {
                $q->whereNotIn('id', $existingAstapIds);
            })
            ->pluck('id');

        $directRegisters = collect();
        if ($astapDirectIds->isNotEmpty()) {
            $directRegisters = \App\Models\AstapRegister::with(['astap.jenisAstap'])
                ->where('is_deleted', 0)
                ->where(function($q) {
                    $q->whereNull('status')
                      ->orWhere(function($sub) {
                          $sub->whereNotIn('status', ['Mutasi Keluar OPD', 'Kemitraan', 'Dihibahkan']);
                      });
                })
                ->whereIn('astap_id', $astapDirectIds)
                ->get();
        }

        return $registers->concat($directRegisters)->unique('id');
    }

    /**
     * Override softDelete: Otomatis soft delete seluruh akun pengguna yang terdaftar pada unit ini.
     */
    public function softDelete(?string $reason = null): bool
    {
        return DB::transaction(function () use ($reason) {
            $user = auth()->user();
            $deleterName = $user 
                ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') 
                : 'Administrator';

            $success = $this->update([
                'is_deleted'    => 1,
                'deleted_by'    => $deleterName,
                'deleted_by_id' => $user?->id,
                'deleted_at'    => now(),
            ]);

            if ($success) {
                $relatedUsers = User::where('unit_id', $this->id)->get();
                $deleterReason = $reason ?: "Otomatis dipindahkan ke Recycle Bin karena Unit / Ruangan \"{$this->nama}\" dihapus.";
                foreach ($relatedUsers as $u) {
                    $u->softDelete($deleterReason);
                }
            }

            return $success;
        });
    }

    /**
     * Override restoreData: Otomatis pulihkan seluruh akun pengguna yang terdaftar pada unit ini.
     */
    public function restoreData(): bool
    {
        return DB::transaction(function () {
            $success = $this->update([
                'is_deleted'    => 0,
                'deleted_by'    => null,
                'deleted_by_id' => null,
                'deleted_at'    => null,
            ]);

            if ($success) {
                $relatedUsers = User::where('unit_id', $this->id)->get();
                foreach ($relatedUsers as $u) {
                    $u->restoreData();
                }
            }

            return $success;
        });
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
            if (!$unit->kepala) return;
            $email = $unit->email ?: (Str::slug($unit->nama, '.') . '@rsudkoesnandi.id');
            
            // Proteksi: Cek apakah email bentrok dengan akun user lain
            $existingUser = User::where('email', $email)->first();
            if ($existingUser && $existingUser->unit_id !== $unit->id) {
                $cleanKode = strtolower(str_replace(['-', ' '], '', $unit->kode_unit ?: 'unit' . $unit->id));
                $email = Str::slug($unit->nama, '.') . '.' . $cleanKode . '@rsudkoesnandi.id';
            }

            User::withoutEvents(function () use ($unit, $email) {
                User::updateOrCreate(
                    ['unit_id' => $unit->id],
                    [
                        'email' => $email,
                        'name' => $unit->kepala,
                        'role' => 'sub_admin',
                        'unit_id' => $unit->id,
                        'penugasan' => 'Sub Admin Ruangan ' . $unit->nama,
                        'status' => 'Aktif',
                        'is_deleted' => 0,
                        'deleted_by' => null,
                        'deleted_by_id' => null,
                        'deleted_at' => null,
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
                $targetEmail = $unit->email ?: $user->email;
                if ($unit->email && $user->email !== $unit->email) {
                    $emailConflict = User::where('email', $unit->email)->where('id', '!=', $user->id)->exists();
                    if ($emailConflict) {
                        $targetEmail = $user->email; // Jangan overwrite jika email bentrok dengan user lain
                    }
                }

                if ($user->name !== $unit->kepala || $user->email !== $targetEmail) {
                    $user->withoutEvents(function () use ($user, $unit, $targetEmail) {
                        $user->update([
                            'name' => $unit->kepala,
                            'email' => $targetEmail,
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

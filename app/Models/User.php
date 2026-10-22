<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, \App\Traits\TrackableSoftDelete;

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
        'is_deleted',
        'deleted_by',
        'deleted_by_id',
        'deleted_at',
    ];

    /**
     * Accessor yang otomatis disertakan saat model diserialisasi ke JSON / Array
     */
    protected $appends = [
        'unit',
        'nip',
    ];

    /**
     * Daftar Definisi Hak Akses Modul & Sub-Aksi Operasional SIMAT-RK (Hierarchical Tree)
     */
    public const AVAILABLE_PERMISSIONS = [
        'astap' => [
            'label' => 'Data ASTAP & Kode 108',
            'icon' => '📦',
            'color' => 'emerald',
            'description' => 'Inventaris aset tetap, form input, barcode QR, export, reklasifikasi, hibah, dan kemitraan',
            'actions' => [
                'view' => [
                    'label' => 'Lihat & Filter Data ASTAP',
                    'desc' => 'Melihat katalog aset, spesifikasi detail, riwayat, dan pencarian',
                    'critical' => false,
                    'default' => true,
                ],
                'create' => [
                    'label' => 'Tambah Data ASTAP Baru',
                    'desc' => 'Menginput aset tetap baru (KIB A s/d F, ATB, Kemitraan)',
                    'critical' => false,
                    'default' => true,
                ],
                'edit' => [
                    'label' => 'Ubah / Edit Data ASTAP',
                    'desc' => 'Memperbarui data teknis, nilai perolehan, dan spesifikasi aset',
                    'critical' => false,
                    'default' => true,
                ],
                'delete' => [
                    'label' => 'Hapus Data ASTAP',
                    'desc' => 'Menghapus aset tetap ke Pusat Data Terhapus (Recycle Bin)',
                    'critical' => true,
                    'default' => false,
                ],
                'export' => [
                    'label' => 'Export Excel & Laporan',
                    'desc' => 'Mengunduh rekap inventaris, format Excel, dan cetak laporan rekap',
                    'critical' => false,
                    'default' => true,
                ],
                'qr' => [
                    'label' => 'Cetak Barcode & QR Label',
                    'desc' => 'Mencetak stiker barcode inventaris untuk fisik barang',
                    'critical' => false,
                    'default' => true,
                ],
                'reklas' => [
                    'label' => 'Reklasifikasi Aset Tetap',
                    'desc' => 'Proses pemindahan antar akun/kode 108 (KDP, reklas masuk/keluar)',
                    'critical' => true,
                    'default' => false,
                ],
                'hibah' => [
                    'label' => 'Kelola Hibah Aset Tetap',
                    'desc' => 'Pencatatan dan verifikasi perolehan aset dari pihak hibah',
                    'critical' => true,
                    'default' => false,
                ],
                'kemitraan' => [
                    'label' => 'Kelola Kemitraan (KSO / Sewa)',
                    'desc' => 'Pencatatan kerja sama pemanfaatan dan sewa aset akun 1.5.2',
                    'critical' => true,
                    'default' => false,
                ],
                'tte' => [
                    'label' => 'Tanda Tangan Elektronik (BSrE)',
                    'desc' => 'Otorisasi verifikasi TTE digital bersertifikat BSrE pada dokumen aset',
                    'critical' => true,
                    'default' => false,
                ],
            ],
        ],
        'distribusi' => [
            'label' => 'Distribusi ASTAP',
            'icon' => '🚚',
            'color' => 'teal',
            'description' => 'Penyaluran aset ke ruangan, verifikasi penerimaan, dan status distribusi',
            'actions' => [
                'view' => [
                    'label' => 'Lihat Daftar Distribusi',
                    'desc' => 'Melihat status pengajuan dan alur distribusi aset ke unit',
                    'critical' => false,
                    'default' => true,
                ],
                'create' => [
                    'label' => 'Buat Distribusi Baru',
                    'desc' => 'Menerbitkan penyaluran aset baru ke unit/ruangan pemohon',
                    'critical' => false,
                    'default' => true,
                ],
                'konfirmasi' => [
                    'label' => 'Konfirmasi Penerimaan Unit',
                    'desc' => 'Memvalidasi serah terima fisik barang telah diterima di ruangan',
                    'critical' => false,
                    'default' => true,
                ],
                'cetak' => [
                    'label' => 'Cetak Dokumen Distribusi',
                    'desc' => 'Mencetak bukti penyerahan barang distribusi ke ruangan',
                    'critical' => false,
                    'default' => true,
                ],
                'delete' => [
                    'label' => 'Batalkan / Hapus Distribusi',
                    'desc' => 'Membatalkan atau menghapus transaksi distribusi yang belum selesai',
                    'critical' => true,
                    'default' => false,
                ],
            ],
        ],
        'bast' => [
            'label' => 'Berita Acara (BAST)',
            'icon' => '📜',
            'color' => 'blue',
            'description' => 'Pembuatan, penandatanganan Berita Acara Serah Terima, dan rekap triwulan',
            'actions' => [
                'view' => [
                    'label' => 'Lihat Arsip BAST',
                    'desc' => 'Melihat dokumen BAST tersimpan, nomor register, dan status legalitas',
                    'critical' => false,
                    'default' => true,
                ],
                'create' => [
                    'label' => 'Buat Dokumen BAST Baru',
                    'desc' => 'Membuat berita acara penyerahan fisik barang antar pihak',
                    'critical' => false,
                    'default' => true,
                ],
                'tte' => [
                    'label' => 'TTE Digital BSrE BAST',
                    'desc' => 'Melakukan penandatanganan elektronik resmi pada lembar BAST',
                    'critical' => true,
                    'default' => false,
                ],
                'cetak' => [
                    'label' => 'Cetak BAST & Rekap Triwulan',
                    'desc' => 'Mencetak fisik BAST dan laporan rekapitulasi berkala',
                    'critical' => false,
                    'default' => true,
                ],
                'delete' => [
                    'label' => 'Hapus / Batalkan BAST',
                    'desc' => 'Menghapus arsip dokumen BAST ke Recycle Bin',
                    'critical' => true,
                    'default' => false,
                ],
            ],
        ],
        'mutasi' => [
            'label' => 'Mutasi Aset',
            'icon' => '🔄',
            'color' => 'amber',
            'description' => 'Mutasi internal antar ruangan RSUD dan mutasi eksternal antar-OPD Pemkab',
            'actions' => [
                'view' => [
                    'label' => 'Lihat Riwayat Mutasi',
                    'desc' => 'Melihat log perpindahan aset internal dan eksternal',
                    'critical' => false,
                    'default' => true,
                ],
                'internal' => [
                    'label' => 'Kelola Mutasi Internal',
                    'desc' => 'Memproses perpindahan aset antar unit/ruangan dalam RSUD',
                    'critical' => false,
                    'default' => true,
                ],
                'eksternal' => [
                    'label' => 'Kelola Mutasi Eksternal (Antar-OPD)',
                    'desc' => 'Memproses hibah/mutasi keluar & masuk dengan SKPD luar RSUD',
                    'critical' => true,
                    'default' => false,
                ],
                'approve' => [
                    'label' => 'Persetujuan / Validasi Mutasi',
                    'desc' => 'Menyetujui permohonan mutasi aset sebelum status diperbarui',
                    'critical' => true,
                    'default' => false,
                ],
                'cetak' => [
                    'label' => 'Cetak Berita Acara Mutasi',
                    'desc' => 'Mencetak dokumen resmi BAST Mutasi Internal & Eksternal',
                    'critical' => false,
                    'default' => true,
                ],
                'delete' => [
                    'label' => 'Hapus / Batalkan Mutasi',
                    'desc' => 'Membatalkan transaksi mutasi aset',
                    'critical' => true,
                    'default' => false,
                ],
            ],
        ],
        'unit' => [
            'label' => 'Unit & Paviliun',
            'icon' => '🏥',
            'color' => 'indigo',
            'description' => 'Katalog data unit, ruangan, paviliun, penanggung jawab, dan cetak KIR',
            'actions' => [
                'view' => [
                    'label' => 'Lihat Katalog Unit & Ruangan',
                    'desc' => 'Melihat daftar ruangan, penanggung jawab, dan lokasi inventaris',
                    'critical' => false,
                    'default' => true,
                ],
                'edit' => [
                    'label' => 'Ubah Data Unit & Pejabat',
                    'desc' => 'Memperbarui nama ruangan, NIP, dan kepala unit penanggung jawab',
                    'critical' => false,
                    'default' => true,
                ],
                'kir' => [
                    'label' => 'Cetak Lembar KIR Ruangan',
                    'desc' => 'Mencetak lembar Kartu Inventaris Ruangan (KIR) resmi',
                    'critical' => false,
                    'default' => true,
                ],
                'delete' => [
                    'label' => 'Hapus Unit / Ruangan',
                    'desc' => 'Menghapus master ruangan dari katalog sistem',
                    'critical' => true,
                    'default' => false,
                ],
            ],
        ],
        'master_data' => [
            'label' => 'Master SIPD (Pengadaan & Rekening)',
            'icon' => '⚙️',
            'color' => 'cyan',
            'description' => 'Data master Jenis Pengadaan, Rekening Belanja SIPD, dan Kode 108',
            'actions' => [
                'view' => [
                    'label' => 'Lihat Master Data SIPD',
                    'desc' => 'Melihat daftar pengadaan, rekening belanja, dan kode klasifikasi',
                    'critical' => false,
                    'default' => true,
                ],
                'jenis_pengadaan' => [
                    'label' => 'Kelola Jenis Pengadaan',
                    'desc' => 'Menambah, mengubah, atau menghapus kategori pengadaan',
                    'critical' => false,
                    'default' => true,
                ],
                'rekening' => [
                    'label' => 'Kelola Rekening Belanja SIPD',
                    'desc' => 'Menambah, mengubah, atau menghapus kode rekening belanja',
                    'critical' => false,
                    'default' => true,
                ],
                'jenis_astap' => [
                    'label' => 'Kelola Jenis ASTAP & Kode 108',
                    'desc' => 'Menambah atau memodifikasi tabel mapping Klasifikasi Kode 108',
                    'critical' => true,
                    'default' => false,
                ],
            ],
        ],
        'users' => [
            'label' => 'Manajemen Pengguna',
            'icon' => '👥',
            'color' => 'rose',
            'description' => 'Kelola akun pegawai, staf operasional, dan hak akses otorisasi',
            'actions' => [
                'view' => [
                    'label' => 'Lihat Daftar Pengguna',
                    'desc' => 'Melihat daftar akun, email, role, dan status aktif pegawai',
                    'critical' => false,
                    'default' => true,
                ],
                'create' => [
                    'label' => 'Tambah Pengguna Baru',
                    'desc' => 'Mendaftarkan akun staf admin atau sub admin ruangan baru',
                    'critical' => false,
                    'default' => true,
                ],
                'edit' => [
                    'label' => 'Ubah Data & Hak Akses User',
                    'desc' => 'Memperbarui profil pegawai dan konfigurasi wewenang akun',
                    'critical' => true,
                    'default' => false,
                ],
                'delete' => [
                    'label' => 'Hapus / Nonaktifkan User',
                    'desc' => 'Menonaktifkan akun atau memindahkan user ke Recycle Bin',
                    'critical' => true,
                    'default' => false,
                ],
            ],
        ],
        'recycle_bin' => [
            'label' => 'Pusat Data Terhapus (Recycle Bin)',
            'icon' => '🗑️',
            'color' => 'emerald',
            'description' => 'Melihat arsip data terhapus, memulihkan (restore), dan menghapus permanen',
            'actions' => [
                'view' => [
                    'label' => 'Lihat Data Terhapus',
                    'desc' => 'Melihat tabulasi aset, mutasi, distribusi, dan unit yang terhapus sementara',
                    'critical' => false,
                    'default' => true,
                ],
                'restore' => [
                    'label' => 'Pulihkan Data (Restore)',
                    'desc' => 'Mengembalikan item yang terhapus kembali ke status aktif operasional',
                    'critical' => false,
                    'default' => true,
                ],
                'force_delete' => [
                    'label' => 'Hapus Permanen (Force Delete)',
                    'desc' => 'Menghapus data secara permanen dari basis data sistem secara mutlak',
                    'critical' => true,
                    'default' => false,
                ],
            ],
        ],
    ];

    /**
     * Template Preset Cepat untuk Kemudahan Master Admin (1-Klik)
     */
    public const ROLE_PRESETS = [
        'full_admin' => [
            'title' => 'Admin Operasional Penuh',
            'icon' => '🛡️',
            'color' => 'cyan',
            'description' => 'Wewenang mutlak seluruh modul dan seluruh sub-aksi operasional pusat.',
        ],
        'inventaris' => [
            'title' => 'Pengelola Inventaris & Sarpras',
            'icon' => '📦',
            'color' => 'emerald',
            'description' => 'Fokus inventarisasi harian (ASTAP, Distribusi, Unit) tanpa hak hapus atau tanda tangan legal.',
        ],
        'keuangan_legal' => [
            'title' => 'Admin BAST & Mutasi Legal',
            'icon' => '📜',
            'color' => 'blue',
            'description' => 'Fokus penyusunan dokumen BAST, Mutasi Antar-OPD, Reklasifikasi, Hibah, dan TTE.',
        ],
        'data_entry' => [
            'title' => 'Staf Input & Data Entry',
            'icon' => '✍️',
            'color' => 'amber',
            'description' => 'Input data baru, edit spesifikasi, cetak label barcode QR, dan export Excel tanpa hak hapus.',
        ],
        'auditor' => [
            'title' => 'Pengawas / Auditor (Read-Only)',
            'icon' => '🔍',
            'color' => 'purple',
            'description' => 'Hanya melihat seluruh katalog inventaris dan mengunduh laporan rekap tanpa izin modifikasi.',
        ],
    ];

    /**
     * Bangun konfigurasi default untuk sebuah preset
     */
    public static function getPresetPermissions(string $presetKey): array
    {
        $tree = self::AVAILABLE_PERMISSIONS;
        $result = [];

        foreach ($tree as $modKey => $modDef) {
            $allActions = array_keys($modDef['actions']);
            $defaultActions = [];
            foreach ($modDef['actions'] as $actKey => $actDef) {
                if (!empty($actDef['default'])) {
                    $defaultActions[] = $actKey;
                }
            }

            switch ($presetKey) {
                case 'full_admin':
                    $result[$modKey] = [
                        'enabled' => true,
                        'actions' => $allActions,
                    ];
                    break;

                case 'inventaris':
                    $isAllowed = in_array($modKey, ['astap', 'distribusi', 'unit']);
                    $actions = [];
                    if ($isAllowed) {
                        // Tidak ada delete, reklas, hibah, kemitraan, tte
                        $actions = array_values(array_filter($defaultActions, fn($a) => !in_array($a, ['delete', 'reklas', 'hibah', 'kemitraan', 'tte'])));
                    }
                    $result[$modKey] = [
                        'enabled' => $isAllowed,
                        'actions' => $actions,
                    ];
                    break;

                case 'keuangan_legal':
                    $isAllowed = in_array($modKey, ['astap', 'bast', 'mutasi']);
                    $actions = [];
                    if ($modKey === 'astap') {
                        $actions = ['view', 'export', 'reklas', 'hibah', 'kemitraan', 'tte'];
                    } elseif ($modKey === 'bast') {
                        $actions = ['view', 'create', 'tte', 'cetak'];
                    } elseif ($modKey === 'mutasi') {
                        $actions = ['view', 'internal', 'eksternal', 'approve', 'cetak'];
                    }
                    $result[$modKey] = [
                        'enabled' => $isAllowed,
                        'actions' => $actions,
                    ];
                    break;

                case 'data_entry':
                    $isAllowed = in_array($modKey, ['astap', 'distribusi']);
                    $actions = [];
                    if ($modKey === 'astap') {
                        $actions = ['view', 'create', 'edit', 'export', 'qr'];
                    } elseif ($modKey === 'distribusi') {
                        $actions = ['view', 'create', 'konfirmasi', 'cetak'];
                    }
                    $result[$modKey] = [
                        'enabled' => $isAllowed,
                        'actions' => $actions,
                    ];
                    break;

                case 'auditor':
                    // Read only untuk seluruh modul
                    $actions = [];
                    if (isset($modDef['actions']['view'])) {
                        $actions[] = 'view';
                    }
                    if (isset($modDef['actions']['export'])) {
                        $actions[] = 'export';
                    }
                    $result[$modKey] = [
                        'enabled' => true,
                        'actions' => $actions,
                    ];
                    break;

                default:
                    $result[$modKey] = [
                        'enabled' => in_array($modKey, ['astap', 'distribusi']),
                        'actions' => $defaultActions,
                    ];
                    break;
            }
        }

        return $result;
    }

    /**
     * Normalisasi format permissions ke struktur hirarki JSON yang aman & terstandarisasi
     */
    public static function normalizePermissions(mixed $rawPermissions): ?array
    {
        if ($rawPermissions === null) {
            return null;
        }

        // Jika dikirim sebagai JSON string (misal dari request body)
        if (is_string($rawPermissions)) {
            $decoded = json_decode($rawPermissions, true);
            if (is_array($decoded)) {
                $rawPermissions = $decoded;
            }
        }

        if (!is_array($rawPermissions)) {
            return null;
        }

        $tree = self::AVAILABLE_PERMISSIONS;
        $normalized = [];

        // Kasus 1: Format lama (flat array of module string keys)
        // Contoh: ["astap", "distribusi", "bast"]
        if (array_is_list($rawPermissions) || (isset($rawPermissions[0]) && is_string($rawPermissions[0]))) {
            foreach ($tree as $modKey => $modDef) {
                $isEnabled = in_array($modKey, $rawPermissions, true);
                $actions = [];
                if ($isEnabled) {
                    // Defaultkan ke seluruh sub-aksi default modul tersebut
                    foreach ($modDef['actions'] as $actKey => $actDef) {
                        if (!empty($actDef['default'])) {
                            $actions[] = $actKey;
                        }
                    }
                }
                $normalized[$modKey] = [
                    'enabled' => $isEnabled,
                    'actions' => $actions,
                ];
            }
            return $normalized;
        }

        // Kasus 2: Format baru (associative array berdasarkan key modul)
        foreach ($tree as $modKey => $modDef) {
            $modInput = $rawPermissions[$modKey] ?? null;
            $isEnabled = false;
            $actions = [];

            if (is_array($modInput)) {
                $isEnabled = !empty($modInput['enabled']);
                $rawActions = $modInput['actions'] ?? [];

                if (is_array($rawActions)) {
                    if (in_array('*', $rawActions, true)) {
                        $actions = array_keys($modDef['actions']);
                    } else {
                        $validActions = array_keys($modDef['actions']);
                        $actions = array_values(array_intersect($rawActions, $validActions));
                    }
                }
            } elseif ($modInput === true || $modInput === 1 || $modInput === '1') {
                $isEnabled = true;
                $actions = array_keys($modDef['actions']);
            }

            $normalized[$modKey] = [
                'enabled' => $isEnabled,
                'actions' => $isEnabled ? $actions : [],
            ];
        }

        return $normalized;
    }

    /**
     * Cek Hak Akses Modul Operasional untuk User (Backward-Compatible)
     */
    public function canAccess(string $module): bool
    {
        // 1. Master Admin memiliki akses mutlak ke seluruh modul
        if ($this->role === 'master_admin') {
            return true;
        }

        // 2. Sub Admin bukan admin operasional pusat
        if ($this->role === 'sub_admin') {
            return false;
        }

        // 3. Admin Operasional: cek permissions
        $permissions = $this->permissions;

        // Fallback backward compatibility jika belum diset permissions
        if ($permissions === null || !is_array($permissions)) {
            return true;
        }

        // Format legacy (flat array)
        if (array_is_list($permissions) || (isset($permissions[0]) && is_string($permissions[0]))) {
            return in_array($module, $permissions, true);
        }

        // Format hirarki baru
        if (isset($permissions[$module])) {
            if (is_array($permissions[$module])) {
                return !empty($permissions[$module]['enabled']);
            }
            return (bool) $permissions[$module];
        }

        return false;
    }

    /**
     * Cek Hak Akses Sub-Aksi Spesifik di dalam Modul (Hierarchical Tree Check)
     * Contoh: $user->canDo('astap', 'delete'), $user->canDo('bast', 'tte')
     */
    public function canDo(string $module, string $action): bool
    {
        // 1. Master Admin memiliki wewenang mutlak seluruh aksi
        if ($this->role === 'master_admin') {
            return true;
        }

        // 2. Sub Admin tidak berwenang pada aksi operasional admin pusat
        if ($this->role === 'sub_admin') {
            return false;
        }

        // 3. Pastikan modul induk memiliki izin akses terlebih dahulu
        if (!$this->canAccess($module)) {
            return false;
        }

        $permissions = $this->permissions;

        // Fallback jika belum diset permissions: izinkan aksi default (non-kritis)
        if ($permissions === null || !is_array($permissions)) {
            $isCritical = self::AVAILABLE_PERMISSIONS[$module]['actions'][$action]['critical'] ?? false;
            return !$isCritical;
        }

        // Format legacy (flat array): izinkan seluruh aksi non-kritis
        if (array_is_list($permissions) || (isset($permissions[0]) && is_string($permissions[0]))) {
            $isCritical = self::AVAILABLE_PERMISSIONS[$module]['actions'][$action]['critical'] ?? false;
            return !$isCritical;
        }

        // Format hirarki baru
        if (!isset($permissions[$module]) || !is_array($permissions[$module])) {
            return false;
        }

        if (empty($permissions[$module]['enabled'])) {
            return false;
        }

        $actions = $permissions[$module]['actions'] ?? [];
        if (!is_array($actions)) {
            return false;
        }

        return in_array('*', $actions, true) || in_array($action, $actions, true);
    }

    /**
     * Ambil daftar seluruh sub-aksi yang diizinkan untuk modul tertentu
     */
    public function getAllowedActions(string $module): array
    {
        if ($this->role === 'master_admin') {
            return array_keys(self::AVAILABLE_PERMISSIONS[$module]['actions'] ?? []);
        }

        if (!$this->canAccess($module)) {
            return [];
        }

        $permissions = $this->permissions;
        $treeActions = array_keys(self::AVAILABLE_PERMISSIONS[$module]['actions'] ?? []);

        if ($permissions === null || !is_array($permissions)) {
            return $treeActions;
        }

        if (array_is_list($permissions) || (isset($permissions[0]) && is_string($permissions[0]))) {
            return $treeActions;
        }

        $modConfig = $permissions[$module] ?? null;
        if (!$modConfig || empty($modConfig['enabled'])) {
            return [];
        }

        $actions = $modConfig['actions'] ?? [];
        if (in_array('*', $actions, true)) {
            return $treeActions;
        }

        return $actions;
    }

    /**
     * Relasi ke master Unit / Ruangan
     */
    public function unitModel()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Accessor atribut unit untuk nama unit/ruangan resmi
     */
    public function getUnitAttribute(): ?string
    {
        return $this->unitModel?->nama ?? ($this->attributes['unit'] ?? null);
    }

    /**
     * Accessor atribut NIP resmi pegawai dari master ruangan
     */
    public function getNipAttribute(): ?string
    {
        return $this->unitModel?->nip ?? ($this->attributes['nip'] ?? null);
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

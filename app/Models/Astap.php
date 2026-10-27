<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Astap extends Model
{
    use HasFactory, \App\Traits\TrackableSoftDelete;

    protected $guarded = ['id'];

    public array $pendingExtensionAttributes = [];

    public static array $extensionAttributes = [
        // Belanja Modal
        'jenis_pengadaan_id',
        'rekening_belanja_id',
        'spk_nomor',
        'spk_tanggal',
        'surat_pesanan_nomor',
        'surat_pesanan_tanggal',
        'kwitansi_nomor',
        'kwitansi_tanggal',
        'faktur_nomor',
        'faktur_tanggal',
        'sp2d_nomor',
        'sp2d_tanggal',
        'bast_dokumen_nomor',
        'bast_dokumen_tanggal',
        'penyedia_nama',
        'penyedia_pemilik',
        'penyedia_rekening_nama',
        'penyedia_rekening_nomor',
        'penyedia_alamat',
        'penyedia_telepon',

        // Hibah
        'hibah_pemberi',
        'hibah_nomor_bast',
        'hibah_tanggal_bast',
        'hibah_keterangan',

        // Pelimpahan SKPD / Mutasi Masuk
        'mutasi_asal',
        'mutasi_nomor_bamb',
        'mutasi_tanggal',
        'mutasi_keterangan',
        'skpd_asal',
        'nomor_bamb',
        'tanggal_bamb',

        // Belanja Barang
        'toko_penyedia',
        'nomor_faktur',
        'tanggal_faktur',
        'total_pembelian',

        // Kemitraan Pihak Ketiga (KSO)
        'mitra_nama',
        'nomor_pks',
        'tanggal_pks',
        'tanggal_mulai',
        'tanggal_selesai',
        'kemitraan_keterangan',
    ];

    protected $casts = [
        'spesifikasi_json' => 'array',
        'is_extracomtable' => 'boolean',
        'is_reklas'        => 'boolean',
        'is_deleted'       => 'integer',
        'deleted_at'       => 'datetime',
        'jumlah_anggaran' => 'decimal:2',
        'jumlah_realisasi' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_realisasi' => 'decimal:2',
        'biaya_administrasi_proyek' => 'decimal:2',
    ];

    protected $appends = [
        'spk_nomor',
        'spk_tanggal',
        'surat_pesanan_nomor',
        'surat_pesanan_tanggal',
        'kwitansi_nomor',
        'kwitansi_tanggal',
        'faktur_nomor',
        'faktur_tanggal',
        'sp2d_nomor',
        'sp2d_tanggal',
        'bast_dokumen_nomor',
        'bast_dokumen_tanggal',
        'bast_nomor',
        'bast_tanggal',
        'penyedia_nama',
        'penyedia_pemilik',
        'penyedia_rekening_nama',
        'penyedia_rekening_nomor',
        'penyedia_alamat',
        'penyedia_telepon',
        'jenis_pengadaan_id',
        'rekening_belanja_id',
        'category',
        'kode_108',
    ];

    /**
     * Izinkan mass assignment untuk kolom ekstensi agar tidak tertahan oleh accessor
     */
    public function isFillable($key)
    {
        if (in_array($key, static::$extensionAttributes, true)) {
            return true;
        }

        return parent::isFillable($key);
    }

    /**
     * Intercept attribute assignment: cegah kolom ekstensi ditulis ke tabel astaps
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, static::$extensionAttributes, true)) {
            $this->pendingExtensionAttributes[$key] = $value;
            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    protected static function booted()
    {
        static::saving(function ($astap) {
            if (array_key_exists('penyedia_telepon', $astap->pendingExtensionAttributes)) {
                $tel = $astap->pendingExtensionAttributes['penyedia_telepon'];
                if ($tel !== null && $tel !== '') {
                    $spec = $astap->spesifikasi_json ?? [];
                    if (is_string($spec)) {
                        $spec = json_decode($spec, true) ?? [];
                    }
                    $spec['penyedia_telepon'] = $tel;
                    $astap->spesifikasi_json = $spec;
                }
            }
        });

        static::saved(function ($astap) {
            if ($astap->isBelanjaModal()) {
                $ext = $astap->pendingExtensionAttributes;
                if (!empty($ext) || !empty($astap->belanjaModal)) {
                    \App\Models\AstapBelanjaModal::updateOrCreate(
                        ['astap_id' => $astap->id],
                        [
                            'jenis_pengadaan_id'     => array_key_exists('jenis_pengadaan_id', $ext) ? $ext['jenis_pengadaan_id'] : ($astap->belanjaModal?->jenis_pengadaan_id),
                            'rekening_belanja_id'    => array_key_exists('rekening_belanja_id', $ext) ? $ext['rekening_belanja_id'] : ($astap->belanjaModal?->rekening_belanja_id),
                            'spk_nomor'              => array_key_exists('spk_nomor', $ext) ? $ext['spk_nomor'] : ($astap->belanjaModal?->spk_nomor),
                            'spk_tanggal'            => array_key_exists('spk_tanggal', $ext) ? static::parseDateInput($ext['spk_tanggal']) : ($astap->belanjaModal?->getRawOriginal('spk_tanggal')),
                            'surat_pesanan_nomor'    => array_key_exists('surat_pesanan_nomor', $ext) ? $ext['surat_pesanan_nomor'] : ($astap->belanjaModal?->surat_pesanan_nomor),
                            'surat_pesanan_tanggal'  => array_key_exists('surat_pesanan_tanggal', $ext) ? static::parseDateInput($ext['surat_pesanan_tanggal']) : ($astap->belanjaModal?->getRawOriginal('surat_pesanan_tanggal')),
                            'kwitansi_nomor'         => array_key_exists('kwitansi_nomor', $ext) ? $ext['kwitansi_nomor'] : ($astap->belanjaModal?->kwitansi_nomor),
                            'kwitansi_tanggal'       => array_key_exists('kwitansi_tanggal', $ext) ? static::parseDateInput($ext['kwitansi_tanggal']) : ($astap->belanjaModal?->getRawOriginal('kwitansi_tanggal')),
                            'faktur_nomor'           => array_key_exists('faktur_nomor', $ext) ? $ext['faktur_nomor'] : ($astap->belanjaModal?->faktur_nomor),
                            'faktur_tanggal'         => array_key_exists('faktur_tanggal', $ext) ? static::parseDateInput($ext['faktur_tanggal']) : ($astap->belanjaModal?->getRawOriginal('faktur_tanggal')),
                            'sp2d_nomor'             => array_key_exists('sp2d_nomor', $ext) ? $ext['sp2d_nomor'] : ($astap->belanjaModal?->sp2d_nomor),
                            'sp2d_tanggal'           => array_key_exists('sp2d_tanggal', $ext) ? static::parseDateInput($ext['sp2d_tanggal']) : ($astap->belanjaModal?->getRawOriginal('sp2d_tanggal')),
                            'bast_dokumen_nomor'     => array_key_exists('bast_dokumen_nomor', $ext) ? $ext['bast_dokumen_nomor'] : ($astap->belanjaModal?->bast_dokumen_nomor),
                            'bast_dokumen_tanggal'   => array_key_exists('bast_dokumen_tanggal', $ext) ? static::parseDateInput($ext['bast_dokumen_tanggal']) : ($astap->belanjaModal?->getRawOriginal('bast_dokumen_tanggal')),
                            'penyedia_nama'          => array_key_exists('penyedia_nama', $ext) ? $ext['penyedia_nama'] : ($astap->belanjaModal?->penyedia_nama),
                            'penyedia_pemilik'       => array_key_exists('penyedia_pemilik', $ext) ? $ext['penyedia_pemilik'] : ($astap->belanjaModal?->penyedia_pemilik),
                            'penyedia_rekening_nama' => array_key_exists('penyedia_rekening_nama', $ext) ? $ext['penyedia_rekening_nama'] : ($astap->belanjaModal?->penyedia_rekening_nama),
                            'penyedia_rekening_nomor'=> array_key_exists('penyedia_rekening_nomor', $ext) ? $ext['penyedia_rekening_nomor'] : ($astap->belanjaModal?->penyedia_rekening_nomor),
                            'penyedia_alamat'        => array_key_exists('penyedia_alamat', $ext) ? $ext['penyedia_alamat'] : ($astap->belanjaModal?->penyedia_alamat),
                        ]
                    );
                }
            }
        });
    }

    /**
     * Helper universal parse string tanggal dari frontend (mendukung dd/mm/yyyy, dd-mm-yyyy, dan yyyy-mm-dd)
     */
    public static function parseDateInput($value): ?string
    {
        if (empty($value)) return null;
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        $value = trim((string)$value);
        if ($value === '' || $value === '-') return null;

        // dd/mm/yyyy atau dd-mm-yyyy
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
        }

        // yyyy-mm-dd
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', (int)$m[1], (int)$m[2], (int)$m[3]);
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $value;
        }
    }

    /**
     * Cek sumber perolehan aset.
     */
    public function isBelanjaModal(): bool
    {
        if ($this->isKemitraan()) {
            return false;
        }
        return empty($this->sumber_dana) || $this->sumber_dana === 'belanja_modal';
    }

    public function isBelanjaBarang(): bool
    {
        return $this->sumber_dana === 'belanja_barang' || $this->sumber_dana === 'belanja_rekening';
    }

    public function isBelanjaRekening(): bool
    {
        return $this->isBelanjaBarang();
    }

    public function isHibah(): bool
    {
        return $this->sumber_dana === 'hibah';
    }

    public function isMutasiMasuk(): bool
    {
        return in_array($this->sumber_dana, ['mutasi_eksternal', 'pelimpahan_skpd', 'pelimpahan', 'mutasi_masuk'], true);
    }

    public function isPelimpahanSkpd(): bool
    {
        return $this->isMutasiMasuk();
    }

    public function isMutasiEksternal(): bool
    {
        return $this->isMutasiMasuk() || $this->mutasiEksternals()->exists() || $this->jenis_reklas === 'MUTASI_EKSTERNAL';
    }

    public function isKemitraan(): bool
    {
        $jenisKode = $this->jenisAstap ? $this->jenisAstap->jenis : '';
        return $this->sumber_dana === 'kemitraan' 
            || str_starts_with($jenisKode, '1.5.2')
            || str_starts_with($this->kode_108 ?? '', '1.5.2')
            || !empty($this->kemitraan);
    }

    public function getSumberDanaLabelAttribute(): string
    {
        if ($this->isKemitraan()) {
            return 'Kemitraan Pihak Ketiga (KSO)';
        }

        return match ($this->sumber_dana) {
            'belanja_barang', 'belanja_rekening'                                => 'Belanja Barang (Perbekalan)',
            'hibah'                                                            => 'Hibah Masuk',
            'mutasi_eksternal', 'pelimpahan_skpd', 'pelimpahan', 'mutasi_masuk' => 'Mutasi Eksternal',
            'kemitraan'                                                        => 'Kemitraan Pihak Ketiga (KSO)',
            default                                                            => 'Belanja Modal (APBD/BLUD)',
        };
    }

    // ─── Extension Relations (Opsi B) ──────────────────────────────
    public function belanjaModal()
    {
        return $this->hasOne(AstapBelanjaModal::class, 'astap_id');
    }

    public function belanjaBarang()
    {
        return $this->hasOne(AstapBelanjaBarang::class, 'astap_id');
    }

    public function mutasiEksternalMasuk()
    {
        return $this->hasOne(MutasiEksternal::class, 'astap_id')->where('tipe', 'masuk')->where('is_deleted', 0)->latestOfMany();
    }

    /**
     * @deprecated Gunakan mutasiEksternalMasuk() atau mutasiEksternals()
     */
    public function pelimpahanSkpd()
    {
        return $this->mutasiEksternalMasuk();
    }

    public function mutasiEksternal()
    {
        return $this->hasOne(MutasiEksternal::class, 'astap_id');
    }

    public function mutasiEksternals()
    {
        return $this->hasMany(MutasiEksternal::class, 'astap_id');
    }

    public function mutasiEksternalKeluar()
    {
        return $this->hasOne(MutasiEksternal::class, 'astap_id')->where('tipe', 'keluar')->where('is_deleted', 0)->latestOfMany();
    }

    public function hibahDetail()
    {
        return $this->hasOne(AstapHibah::class, 'astap_id')->where('tipe_hibah', 'masuk');
    }

    // ─── Smart Accessors for Backward Compatibility ───────────────
    public function getJenisPengadaanIdAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['jenis_pengadaan_id'] ?? ($this->belanjaModal?->jenis_pengadaan_id ?? null));
    }

    public function getRekeningBelanjaIdAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['rekening_belanja_id'] ?? ($this->belanjaModal?->rekening_belanja_id ?? null));
    }

    public function getPenyediaNamaAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['penyedia_nama'] ?? ($this->belanjaModal?->penyedia_nama ?? ($this->belanjaBarang?->toko_penyedia ?? null)));
    }

    public function getPenyediaPemilikAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['penyedia_pemilik'] ?? ($this->belanjaModal?->penyedia_pemilik ?? null));
    }

    public function getPenyediaRekeningNamaAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['penyedia_rekening_nama'] ?? ($this->belanjaModal?->penyedia_rekening_nama ?? null));
    }

    public function getPenyediaRekeningNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['penyedia_rekening_nomor'] ?? ($this->belanjaModal?->penyedia_rekening_nomor ?? null));
    }

    public function getPenyediaAlamatAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['penyedia_alamat'] ?? ($this->belanjaModal?->penyedia_alamat ?? null));
    }

    public function getPenyediaTeleponAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['penyedia_telepon'] ?? ($this->spesifikasi_json['penyedia_telepon'] ?? ($this->spesifikasi_json['penyedia_kontak'] ?? null)));
    }

    public function getSpkNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['spk_nomor'] ?? ($this->belanjaModal?->spk_nomor ?? null));
    }

    public function getSpkTanggalAttribute($val = null)
    {
        $d = $val ?? ($this->pendingExtensionAttributes['spk_tanggal'] ?? ($this->belanjaModal?->getRawOriginal('spk_tanggal') ?? null));
        return $d ? substr((string)$d, 0, 10) : null;
    }

    public function getSuratPesananNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['surat_pesanan_nomor'] ?? ($this->belanjaModal?->surat_pesanan_nomor ?? null));
    }

    public function getSuratPesananTanggalAttribute($val = null)
    {
        $d = $val ?? ($this->pendingExtensionAttributes['surat_pesanan_tanggal'] ?? ($this->belanjaModal?->getRawOriginal('surat_pesanan_tanggal') ?? null));
        return $d ? substr((string)$d, 0, 10) : null;
    }

    public function getKwitansiNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['kwitansi_nomor'] ?? ($this->belanjaModal?->kwitansi_nomor ?? null));
    }

    public function getKwitansiTanggalAttribute($val = null)
    {
        $d = $val ?? ($this->pendingExtensionAttributes['kwitansi_tanggal'] ?? ($this->belanjaModal?->getRawOriginal('kwitansi_tanggal') ?? null));
        return $d ? substr((string)$d, 0, 10) : null;
    }

    public function getFakturNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['faktur_nomor'] ?? ($this->belanjaModal?->faktur_nomor ?? ($this->belanjaBarang?->nomor_faktur ?? null)));
    }

    public function getFakturTanggalAttribute($val = null)
    {
        $d = $val ?? ($this->pendingExtensionAttributes['faktur_tanggal'] ?? ($this->belanjaModal?->getRawOriginal('faktur_tanggal') ?? ($this->belanjaBarang?->tanggal_faktur ?? null)));
        return $d ? substr((string)$d, 0, 10) : null;
    }

    public function getSp2dNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['sp2d_nomor'] ?? ($this->belanjaModal?->sp2d_nomor ?? null));
    }

    public function getSp2dTanggalAttribute($val = null)
    {
        $d = $val ?? ($this->pendingExtensionAttributes['sp2d_tanggal'] ?? ($this->belanjaModal?->getRawOriginal('sp2d_tanggal') ?? null));
        return $d ? substr((string)$d, 0, 10) : null;
    }

    public function getBastDokumenNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['bast_dokumen_nomor'] ?? ($this->belanjaModal?->bast_dokumen_nomor ?? ($this->hibahDetail?->nomor_bast ?? ($this->mutasiEksternalMasuk?->nomor_bamb ?? null))));
    }

    public function getBastDokumenTanggalAttribute($val = null)
    {
        $d = $val ?? ($this->pendingExtensionAttributes['bast_dokumen_tanggal'] ?? ($this->belanjaModal?->getRawOriginal('bast_dokumen_tanggal') ?? ($this->hibahDetail?->tanggal_bast ?? ($this->mutasiEksternalMasuk?->tanggal_bamb ?? null))));
        return $d ? substr((string)$d, 0, 10) : null;
    }

    public function getBastNomorAttribute($val = null)
    {
        return $val ?? $this->bast_dokumen_nomor;
    }

    public function getBastTanggalAttribute($val = null)
    {
        return $val ?? $this->bast_dokumen_tanggal;
    }

    public function getHibahPemberiAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['hibah_pemberi'] ?? ($this->hibahDetail?->pihak_hibah ?? null));
    }

    public function getHibahNomorBastAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['hibah_nomor_bast'] ?? ($this->hibahDetail?->nomor_bast ?? null));
    }

    public function getHibahTanggalBastAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['hibah_tanggal_bast'] ?? ($this->hibahDetail?->tanggal_bast ?? null));
    }

    public function getHibahKeteranganAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['hibah_keterangan'] ?? ($this->hibahDetail?->keterangan ?? null));
    }

    public function getMutasiAsalAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['mutasi_asal'] ?? ($this->mutasiEksternalMasuk?->opd_asal ?? ($this->mutasiEksternal?->opd_asal ?? null)));
    }

    public function getMutasiNomorBambAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['mutasi_nomor_bamb'] ?? ($this->mutasiEksternalMasuk?->nomor_bamb ?? ($this->mutasiEksternal?->nomor_bamb ?? null)));
    }

    public function getMutasiTanggalAttribute($val = null)
    {
        $d = $val ?? ($this->pendingExtensionAttributes['mutasi_tanggal'] ?? ($this->mutasiEksternalMasuk?->tanggal_mutasi ?? ($this->mutasiEksternal?->tanggal_mutasi ?? null)));
        return $d ? ($d instanceof \Carbon\Carbon ? $d->format('Y-m-d') : substr((string)$d, 0, 10)) : null;
    }

    public function getMutasiKeteranganAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['mutasi_keterangan'] ?? ($this->mutasiEksternalMasuk?->alasan_mutasi ?? ($this->mutasiEksternal?->alasan_mutasi ?? null)));
    }

    // ─── Relations via Extension Table ─────────────────────────────
    public function jenisPengadaan()
    {
        return $this->hasOneThrough(
            JenisPengadaan::class,
            AstapBelanjaModal::class,
            'astap_id',
            'id',
            'id',
            'jenis_pengadaan_id'
        );
    }

    public function rekeningBelanja()
    {
        return $this->hasOneThrough(
            RekeningBelanja::class,
            AstapBelanjaModal::class,
            'astap_id',
            'id',
            'id',
            'rekening_belanja_id'
        );
    }

    public function jenisAstap()
    {
        return $this->belongsTo(JenisAstap::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registers()
    {
        return $this->hasMany(AstapRegister::class)
            ->where('is_deleted', 0)
            ->orderBy('no_register_int', 'asc')
            ->orderBy('id', 'asc');
    }

    public function allRegisters()
    {
        return $this->hasMany(AstapRegister::class)
            ->orderBy('no_register_int', 'asc')
            ->orderBy('id', 'asc');
    }

    public function reklas()
    {
        return $this->hasMany(AstapReklas::class)->orderBy('tanggal_reklas', 'desc');
    }

    public function hibahs()
    {
        return $this->hasMany(AstapHibah::class, 'astap_id')->orderBy('tanggal_bast', 'desc');
    }

    public function kemitraan()
    {
        return $this->hasOne(AstapKemitraan::class, 'astap_id');
    }

    public function kemitraans()
    {
        return $this->hasMany(AstapKemitraan::class, 'astap_id');
    }

    public function objekKemitraans()
    {
        return $this->hasMany(AstapKemitraan::class, 'objek_astap_id');
    }

    public function getKode108Attribute(): string
    {
        return $this->jenisAstap ? ($this->jenisAstap->sub_sub_rincian_objek ?? '') : '';
    }

    public function getCategoryAttribute(): string
    {
        $jenisKode = $this->jenisAstap ? (string) ($this->jenisAstap->jenis ?? '') : '';
        $subSubKode = $this->jenisAstap ? (string) ($this->jenisAstap->sub_sub_rincian_objek ?? '') : '';
        $fullKode = (string) ($this->kode_108 ?: ($this->kode_barang ?: ($subSubKode ?: $jenisKode)));

        // 0. Akun 1.5.4 (Aset Lain-Lain) & 1.5.3 (ATB) memiliki hirarki mutlak di luar Aset Tetap Operasional (1.3.x)
        if (str_starts_with($fullKode, '1.5.4') || str_starts_with($jenisKode, '1.5.4')) {
            return 'ASET LAIN';
        }
        if (str_starts_with($fullKode, '1.5.3') || str_starts_with($jenisKode, '1.5.3')) {
            return 'ATB';
        }

        $spec = is_array($this->spesifikasi_json) ? $this->spesifikasi_json : (json_decode($this->spesifikasi_json ?? '[]', true) ?: []);

        // Jika dideklarasikan secara eksplisit di spesifikasi sebagai ASET LAIN atau ATB
        if (!empty($spec['kategori_kib'])) {
            $kib = strtoupper(trim((string) $spec['kategori_kib']));
            if ($kib === 'ASET LAIN' || $kib === 'ASET LAIN-LAIN' || $kib === 'ASET LAINNYA' || str_contains($kib, '1.5.4')) {
                return 'ASET LAIN';
            }
            if ($kib === 'ATB' || str_contains($kib, 'TIDAK BERWUJUD') || str_contains($kib, '1.5.3')) {
                return 'ATB';
            }
        }

        // Jika transaksi aset secara eksplisit merupakan Reklasifikasi Ekstrakomptabel
        if ($this->jenis_reklas === 'EKSTRAKOMPTABEL') {
            return 'EXTRACOM';
        }

        $hasFilledItem = function ($key, ...$nameFields) use ($spec) {
            if (empty($spec[$key]) || !is_array($spec[$key])) return false;
            foreach ($spec[$key] as $row) {
                if (is_array($row)) {
                    foreach ($nameFields as $field) {
                        if (!empty($row[$field])) {
                            $clean = trim((string)$row[$field]);
                            if ($clean !== '' && $clean !== '-' && $clean !== '--' && $clean !== '- -') {
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        };

        // 1. Prioritaskan repeater spesifikasi fisik yang nyata dan memiliki nama terisi (abaikan nilai dummy '-')
        if ($hasFilledItem('gedung_items', 'gedung_nama_barang', 'gedung_nama_bangunan')) return 'KIB C';
        if ($hasFilledItem('lainnya_items', 'lainnya_nama_barang', 'lainnya_judul', 'judul', 'lainnya_judul_pencipta')) return 'KIB E';
        if ($hasFilledItem('mesin_items', 'mesin_nama_barang')) return 'KIB B';
        if ($hasFilledItem('jaringan_items', 'jaringan_nama_barang', 'jaringan_nama')) return 'KIB D';
        if ($hasFilledItem('tanah_items', 'tanah_nama_barang', 'tanah_penggunaan')) return 'KIB A';

        // 2. Periksa deklarasi kategori_kib di spesifikasi_json
        if (!empty($spec['kategori_kib'])) {
            $kib = strtoupper((string) $spec['kategori_kib']);
            if (str_contains($kib, 'KIB C') || str_contains($kib, 'GEDUNG') || str_contains($kib, 'BANGUNAN')) return 'KIB C';
            if (str_contains($kib, 'KIB B') || str_contains($kib, 'MESIN') || str_contains($kib, 'PERALATAN')) return 'KIB B';
            if (str_contains($kib, 'KIB A') || str_contains($kib, 'TANAH')) return 'KIB A';
            if (str_contains($kib, 'KIB D') || str_contains($kib, 'JARINGAN') || str_contains($kib, 'JALAN')) return 'KIB D';
            if (str_contains($kib, 'KIB E') || (str_contains($kib, 'LAINNYA') && !str_contains($kib, 'ASET LAIN'))) return 'KIB E';
        }

        // KIB yang TIDAK PERNAH boleh menjadi EXTRACOM menurut Standar Akuntansi Pemerintahan (SAP)
        if (str_starts_with($jenisKode, '1.3.1') || str_starts_with($fullKode, '1.3.1')) return 'KIB A';
        if (str_starts_with($jenisKode, '1.3.3') || str_starts_with($fullKode, '1.3.3')) return 'KIB C';
        if (str_starts_with($jenisKode, '1.3.4') || str_starts_with($fullKode, '1.3.4')) return 'KIB D';
        if (str_starts_with($jenisKode, '1.3.6') || str_starts_with($fullKode, '1.3.6')) return 'KIB F';
        
        // Akun 1.5.2 (Kemitraan Dengan Pihak Ketiga) -> Petakan ke wujud fisik KIB-nya
        if (str_starts_with($jenisKode, '1.5.2') || str_starts_with($fullKode, '1.5.2')) {
            // Cek kode sub-sub rincian:
            // 001 = Tanah, 002 = Peralatan & Mesin, 003 = Gedung & Bangunan, 004 = Jalan/Irigasi/Jaringan, 005 = Aset Tetap Lainnya
            if (str_contains($fullKode, '.01.01.001') || str_contains($fullKode, '.01.02.001') || str_ends_with($fullKode, '.001') || stripos($this->nama_barang, 'tanah') !== false) {
                return 'KIB A';
            }
            if (str_contains($fullKode, '.01.01.002') || str_contains($fullKode, '.01.02.002') || str_ends_with($fullKode, '.002') || stripos($this->nama_barang, 'mesin') !== false || stripos($this->nama_barang, 'peralatan') !== false) {
                return 'KIB B';
            }
            if (str_contains($fullKode, '.01.01.003') || str_contains($fullKode, '.01.02.003') || str_ends_with($fullKode, '.003') || stripos($this->nama_barang, 'gedung') !== false || stripos($this->nama_barang, 'bangunan') !== false) {
                return 'KIB C';
            }
            if (str_contains($fullKode, '.01.01.004') || str_contains($fullKode, '.01.02.004') || str_ends_with($fullKode, '.004') || stripos($this->nama_barang, 'jaringan') !== false || stripos($this->nama_barang, 'irigasi') !== false || stripos($this->nama_barang, 'jalan') !== false) {
                return 'KIB D';
            }
            if (str_contains($fullKode, '.01.01.005') || str_contains($fullKode, '.01.02.005') || str_ends_with($fullKode, '.005')) {
                return 'KIB E';
            }
            
            return 'KIB B';
        }

        if (str_starts_with($jenisKode, '1.5.3') || str_starts_with($fullKode, '1.5.3')) return 'ATB';
        if (str_starts_with($jenisKode, '1.5.4') || str_starts_with($fullKode, '1.5.4')) return 'ASET LAIN';

        if ($this->is_extracomtable) {
            return 'EXTRACOM';
        }

        if (str_starts_with($jenisKode, '1.3.2') || str_starts_with($fullKode, '1.3.2')) return 'KIB B';
        if (str_starts_with($jenisKode, '1.3.5') || str_starts_with($fullKode, '1.3.5')) return 'KIB E';

        return 'KIB B';
    }

    public function setCategoryAttribute($value): void
    {
        // Category adalah atribut dinamis (accessor) yang diturunkan dari jenis_astap_id dan is_extracomtable.
        // Mutator ini mencegah Eloquent mencoba menulis kolom 'category' ke database.
    }
}

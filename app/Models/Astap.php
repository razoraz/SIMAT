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
        static::saved(function ($astap) {
            if ($astap->isBelanjaModal()) {
                $ext = $astap->pendingExtensionAttributes;
                if (!empty($ext) || !empty($astap->belanjaModal)) {
                    \App\Models\AstapBelanjaModal::updateOrCreate(
                        ['astap_id' => $astap->id],
                        array_filter([
                            'jenis_pengadaan_id'     => $ext['jenis_pengadaan_id'] ?? ($astap->belanjaModal?->jenis_pengadaan_id),
                            'rekening_belanja_id'    => $ext['rekening_belanja_id'] ?? ($astap->belanjaModal?->rekening_belanja_id),
                            'spk_nomor'              => $ext['spk_nomor'] ?? ($astap->belanjaModal?->spk_nomor),
                            'spk_tanggal'            => isset($ext['spk_tanggal']) ? static::parseDateInput($ext['spk_tanggal']) : ($astap->belanjaModal?->getRawOriginal('spk_tanggal')),
                            'surat_pesanan_nomor'    => $ext['surat_pesanan_nomor'] ?? ($astap->belanjaModal?->surat_pesanan_nomor),
                            'surat_pesanan_tanggal'  => isset($ext['surat_pesanan_tanggal']) ? static::parseDateInput($ext['surat_pesanan_tanggal']) : ($astap->belanjaModal?->getRawOriginal('surat_pesanan_tanggal')),
                            'kwitansi_nomor'         => $ext['kwitansi_nomor'] ?? ($astap->belanjaModal?->kwitansi_nomor),
                            'kwitansi_tanggal'       => isset($ext['kwitansi_tanggal']) ? static::parseDateInput($ext['kwitansi_tanggal']) : ($astap->belanjaModal?->getRawOriginal('kwitansi_tanggal')),
                            'faktur_nomor'           => $ext['faktur_nomor'] ?? ($astap->belanjaModal?->faktur_nomor),
                            'faktur_tanggal'         => isset($ext['faktur_tanggal']) ? static::parseDateInput($ext['faktur_tanggal']) : ($astap->belanjaModal?->getRawOriginal('faktur_tanggal')),
                            'sp2d_nomor'             => $ext['sp2d_nomor'] ?? ($astap->belanjaModal?->sp2d_nomor),
                            'sp2d_tanggal'           => isset($ext['sp2d_tanggal']) ? static::parseDateInput($ext['sp2d_tanggal']) : ($astap->belanjaModal?->getRawOriginal('sp2d_tanggal')),
                            'bast_dokumen_nomor'     => $ext['bast_dokumen_nomor'] ?? ($astap->belanjaModal?->bast_dokumen_nomor),
                            'bast_dokumen_tanggal'   => isset($ext['bast_dokumen_tanggal']) ? static::parseDateInput($ext['bast_dokumen_tanggal']) : ($astap->belanjaModal?->getRawOriginal('bast_dokumen_tanggal')),
                            'penyedia_nama'          => $ext['penyedia_nama'] ?? ($astap->belanjaModal?->penyedia_nama),
                            'penyedia_pemilik'       => $ext['penyedia_pemilik'] ?? ($astap->belanjaModal?->penyedia_pemilik),
                            'penyedia_rekening_nama' => $ext['penyedia_rekening_nama'] ?? ($astap->belanjaModal?->penyedia_rekening_nama),
                            'penyedia_rekening_nomor'=> $ext['penyedia_rekening_nomor'] ?? ($astap->belanjaModal?->penyedia_rekening_nomor),
                            'penyedia_alamat'        => $ext['penyedia_alamat'] ?? ($astap->belanjaModal?->penyedia_alamat),
                        ], fn($val) => !is_null($val))
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

    public function isPelimpahanSkpd(): bool
    {
        return $this->sumber_dana === 'pelimpahan_skpd' || $this->sumber_dana === 'mutasi_masuk';
    }

    public function isMutasiMasuk(): bool
    {
        return $this->isPelimpahanSkpd();
    }

    public function getSumberDanaLabelAttribute(): string
    {
        return match ($this->sumber_dana) {
            'belanja_barang', 'belanja_rekening' => 'Belanja Barang (Perbekalan)',
            'hibah'                             => 'Hibah Pihak Ketiga',
            'pelimpahan_skpd', 'mutasi_masuk'   => 'Pelimpahan SKPD Luar',
            default                             => 'Belanja Modal (APBD/BLUD)',
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

    public function pelimpahanSkpd()
    {
        return $this->hasOne(AstapPelimpahanSkpd::class, 'astap_id');
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

    public function getSpkNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['spk_nomor'] ?? ($this->belanjaModal?->spk_nomor ?? null));
    }

    public function getSpkTanggalAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['spk_tanggal'] ?? ($this->belanjaModal?->spk_tanggal ?? null));
    }

    public function getSuratPesananNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['surat_pesanan_nomor'] ?? ($this->belanjaModal?->surat_pesanan_nomor ?? null));
    }

    public function getSuratPesananTanggalAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['surat_pesanan_tanggal'] ?? ($this->belanjaModal?->surat_pesanan_tanggal ?? null));
    }

    public function getKwitansiNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['kwitansi_nomor'] ?? ($this->belanjaModal?->kwitansi_nomor ?? null));
    }

    public function getKwitansiTanggalAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['kwitansi_tanggal'] ?? ($this->belanjaModal?->kwitansi_tanggal ?? null));
    }

    public function getFakturNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['faktur_nomor'] ?? ($this->belanjaModal?->faktur_nomor ?? ($this->belanjaBarang?->nomor_faktur ?? null)));
    }

    public function getFakturTanggalAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['faktur_tanggal'] ?? ($this->belanjaModal?->faktur_tanggal ?? ($this->belanjaBarang?->tanggal_faktur ?? null)));
    }

    public function getSp2dNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['sp2d_nomor'] ?? ($this->belanjaModal?->sp2d_nomor ?? null));
    }

    public function getSp2dTanggalAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['sp2d_tanggal'] ?? ($this->belanjaModal?->sp2d_tanggal ?? null));
    }

    public function getBastDokumenNomorAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['bast_dokumen_nomor'] ?? ($this->belanjaModal?->bast_dokumen_nomor ?? ($this->hibahDetail?->nomor_bast ?? ($this->pelimpahanSkpd?->nomor_bamb ?? null))));
    }

    public function getBastDokumenTanggalAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['bast_dokumen_tanggal'] ?? ($this->belanjaModal?->bast_dokumen_tanggal ?? ($this->hibahDetail?->tanggal_bast ?? ($this->pelimpahanSkpd?->tanggal_bamb ?? null))));
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
        return $val ?? ($this->pendingExtensionAttributes['mutasi_asal'] ?? ($this->pelimpahanSkpd?->skpd_asal ?? null));
    }

    public function getMutasiNomorBambAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['mutasi_nomor_bamb'] ?? ($this->pelimpahanSkpd?->nomor_bamb ?? null));
    }

    public function getMutasiTanggalAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['mutasi_tanggal'] ?? ($this->pelimpahanSkpd?->tanggal_bamb ?? null));
    }

    public function getMutasiKeteranganAttribute($val = null)
    {
        return $val ?? ($this->pendingExtensionAttributes['mutasi_keterangan'] ?? ($this->pelimpahanSkpd?->keterangan ?? null));
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
        return $this->hasMany(AstapRegister::class)->orderBy('no_register_int', 'asc')->orderBy('id', 'asc');
    }

    public function reklas()
    {
        return $this->hasMany(AstapReklas::class)->orderBy('tanggal_reklas', 'desc');
    }

    public function hibahs()
    {
        return $this->hasMany(AstapHibah::class, 'astap_id')->orderBy('tanggal_bast', 'desc');
    }

    public function getKode108Attribute(): string
    {
        return $this->jenisAstap ? ($this->jenisAstap->sub_sub_rincian_objek ?? '') : '';
    }

    public function getCategoryAttribute(): string
    {
        $jenisKode = $this->jenisAstap ? $this->jenisAstap->jenis : '';

        // KIB yang TIDAK PERNAH boleh menjadi EXTRACOM menurut Standar Akuntansi Pemerintahan (SAP)
        if (str_starts_with($jenisKode, '1.3.1')) return 'KIB A';
        if (str_starts_with($jenisKode, '1.3.3')) return 'KIB C';
        if (str_starts_with($jenisKode, '1.3.4')) return 'KIB D';
        if (str_starts_with($jenisKode, '1.3.6')) return 'KIB F';
        if (str_starts_with($jenisKode, '1.5.2')) return 'KEMITRAAN';
        if (str_starts_with($jenisKode, '1.5.3')) return 'ATB';
        if (str_starts_with($jenisKode, '1.5.4')) return 'ASET LAIN';

        // Hanya KIB B (Peralatan & Mesin) atau KIB E (Aset Tetap Lainnya) yang bisa EXTRACOM
        if ($this->is_extracomtable) {
            return 'EXTRACOM';
        }

        if (str_starts_with($jenisKode, '1.3.2')) return 'KIB B';
        if (str_starts_with($jenisKode, '1.3.5')) return 'KIB E';

        return 'KIB B';
    }
}

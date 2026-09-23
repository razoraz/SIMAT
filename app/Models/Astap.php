<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Astap extends Model
{
    use HasFactory, \App\Traits\TrackableSoftDelete;

    protected $guarded = ['id'];

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
        'spk_tanggal' => 'date:d/m/Y',
        'surat_pesanan_tanggal' => 'date:d/m/Y',
        'kwitansi_tanggal' => 'date:d/m/Y',
        'faktur_tanggal' => 'date:d/m/Y',
        'sp2d_tanggal' => 'date:d/m/Y',
        'bast_dokumen_tanggal' => 'date:d/m/Y',
        'hibah_tanggal_bast' => 'date:d/m/Y',
    ];

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

    public function setSpkTanggalAttribute($value)
    {
        $this->attributes['spk_tanggal'] = static::parseDateInput($value);
    }

    public function setSuratPesananTanggalAttribute($value)
    {
        $this->attributes['surat_pesanan_tanggal'] = static::parseDateInput($value);
    }

    public function setKwitansiTanggalAttribute($value)
    {
        $this->attributes['kwitansi_tanggal'] = static::parseDateInput($value);
    }

    public function setFakturTanggalAttribute($value)
    {
        $this->attributes['faktur_tanggal'] = static::parseDateInput($value);
    }

    public function setSp2dTanggalAttribute($value)
    {
        $this->attributes['sp2d_tanggal'] = static::parseDateInput($value);
    }

    public function setBastDokumenTanggalAttribute($value)
    {
        $this->attributes['bast_dokumen_tanggal'] = static::parseDateInput($value);
    }

    public function setHibahTanggalBastAttribute($value)
    {
        $this->attributes['hibah_tanggal_bast'] = static::parseDateInput($value);
    }

    /**
     * Cek apakah aset ini berasal dari hibah.
     */
    public function isHibah(): bool
    {
        return $this->sumber_dana === 'hibah';
    }

    public function jenisPengadaan()
    {
        return $this->belongsTo(JenisPengadaan::class);
    }

    public function rekeningBelanja()
    {
        return $this->belongsTo(RekeningBelanja::class);
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
        if (str_starts_with($jenisKode, '1.5.3')) return 'ATB';

        // Hanya KIB B (Peralatan & Mesin) atau KIB E (Aset Tetap Lainnya) yang bisa EXTRACOM
        if ($this->is_extracomtable) {
            return 'EXTRACOM';
        }

        if (str_starts_with($jenisKode, '1.3.2')) return 'KIB B';
        if (str_starts_with($jenisKode, '1.3.5')) return 'KIB E';

        return 'KIB B';
    }
}

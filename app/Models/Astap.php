<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Astap extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'spesifikasi_json' => 'array',
        'is_extracomtable' => 'boolean',
        'jumlah_anggaran' => 'decimal:2',
        'jumlah_realisasi' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_realisasi' => 'decimal:2',
        'biaya_administrasi_proyek' => 'decimal:2',
        'spk_tanggal' => 'date',
        'surat_pesanan_tanggal' => 'date',
        'kwitansi_tanggal' => 'date',
        'faktur_tanggal' => 'date',
        'sp2d_tanggal' => 'date',
        'bast_dokumen_tanggal' => 'date',
    ];

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
        return $this->hasMany(AstapRegister::class);
    }

    public function getKode108Attribute(): string
    {
        return $this->jenisAstap ? ($this->jenisAstap->sub_sub_rincian_objek ?? '') : '';
    }

    public function getCategoryAttribute(): string
    {
        $jenisKode = $this->jenisAstap ? $this->jenisAstap->jenis : '';

        // Cek jika spesifikasi memiliki array mesin_items multi-item
        $spec = is_array($this->spesifikasi_json) ? $this->spesifikasi_json : (json_decode($this->spesifikasi_json, true) ?? []);
        if (!empty($spec['mesin_items']) && is_array($spec['mesin_items']) && count($spec['mesin_items']) > 0) {
            $hasKibB = false;
            $hasExtracom = false;
            foreach ($spec['mesin_items'] as $m) {
                $price = floatval($m['mesin_nilai_satuan'] ?? 0);
                if ($price >= 300000) {
                    $hasKibB = true;
                } else {
                    $hasExtracom = true;
                }
            }
            if ($hasKibB) {
                return 'KIB B';
            } elseif ($hasExtracom) {
                return 'EXTRACOM';
            }
        }

        if ($this->is_extracomtable || (str_starts_with($jenisKode, '1.3.2') && $this->harga_satuan > 0 && $this->harga_satuan < 300000)) {
            return 'EXTRACOM';
        }

        if (str_starts_with($jenisKode, '1.3.1')) return 'KIB A';
        if (str_starts_with($jenisKode, '1.3.2')) return 'KIB B';
        if (str_starts_with($jenisKode, '1.3.3')) return 'KIB C';
        if (str_starts_with($jenisKode, '1.3.4')) return 'KIB D';
        if (str_starts_with($jenisKode, '1.3.5')) return 'KIB E';
        if (str_starts_with($jenisKode, '1.3.6')) return 'KIB F';
        if (str_starts_with($jenisKode, '1.5.3')) return 'ATB';

        return 'KIB B';
    }
}

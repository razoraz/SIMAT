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
}

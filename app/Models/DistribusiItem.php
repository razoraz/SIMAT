<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistribusiItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'nibar_list' => 'array',
    ];

    /**
     * Relasi ke Header Distribusi
     */
    public function distribusi()
    {
        return $this->belongsTo(Distribusi::class);
    }

    /**
     * Relasi ke Master Data ASTAP (Cukup simpan astap_id)
     */
    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }

    /**
     * Accessor: Nama Barang (Langsung dari data Master ASTAP)
     */
    public function getNamaBarangAttribute()
    {
        return $this->astap?->nama_barang ?? '-';
    }

    /**
     * Accessor: Kode Rekening 108 (Langsung dari data Master ASTAP)
     */
    public function getKodeBarangAttribute()
    {
        return $this->astap?->kode_108 ?? '-';
    }

    /**
     * Accessor: Jenis ASTAP (Langsung dari Master ASTAP -> JenisAstap)
     */
    public function getJenisAstapNamaAttribute()
    {
        return $this->astap?->jenisAstap?->nama_jenis ?? '-';
    }

    /**
     * Accessor: Satuan Barang (Langsung dari Master ASTAP)
     */
    public function getSatuanAttribute()
    {
        return $this->astap?->satuan ?? 'Unit';
    }

    /**
     * Accessor: Spesifikasi / Merk (Langsung dari Master ASTAP)
     */
    public function getMerkTypeAttribute()
    {
        $spec = is_array($this->astap?->spesifikasi_json) 
            ? $this->astap->spesifikasi_json 
            : (json_decode($this->astap?->spesifikasi_json ?? '', true) ?? []);
        return $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? '-'));
    }

    /**
     * Relasi/Helper: Mengambil Model Register NIBAR dari data ASTAP
     */
    public function getRegistersAttribute()
    {
        if (empty($this->nibar_list)) {
            return collect();
        }
        return AstapRegister::whereIn('nibar', $this->nibar_list)->get();
    }
}

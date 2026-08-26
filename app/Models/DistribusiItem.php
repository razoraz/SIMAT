<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistribusiItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Tidak ada lagi cast nibar_list — data NIBAR kini ada di distribusi_item_registers

    /**
     * Relasi ke Header Distribusi
     */
    public function distribusi()
    {
        return $this->belongsTo(Distribusi::class);
    }

    /**
     * Relasi ke Master Data ASTAP (cukup simpan astap_id)
     */
    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }

    /**
     * Relasi ke pivot unit fisik NIBAR yang terdistribusi
     * (menggantikan kolom JSON nibar_list)
     */
    public function registers()
    {
        return $this->hasMany(DistribusiItemRegister::class);
    }

    /**
     * Akses langsung ke AstapRegister melalui pivot
     */
    public function astapRegisters()
    {
        return $this->hasManyThrough(
            AstapRegister::class,
            DistribusiItemRegister::class,
            'distribusi_item_id',   // FK di distribusi_item_registers
            'id',                   // PK di astap_registers
            'id',                   // PK di distribusi_items
            'astap_register_id'     // FK di distribusi_item_registers
        );
    }

    // ── Accessor: Baca dari relasi, tidak duplikasi data ──

    public function getNamaBarangAttribute(): string
    {
        return $this->astap?->nama_barang ?? '-';
    }

    public function getKodeBarangAttribute(): string
    {
        return $this->astap?->kode_108 ?? '-';
    }

    public function getJenisAstapNamaAttribute(): string
    {
        return $this->astap?->jenisAstap?->nama_jenis ?? '-';
    }

    public function getSatuanAttribute(): string
    {
        return $this->astap?->satuan ?? 'Unit';
    }

    public function getMerkTypeAttribute(): string
    {
        $spec = is_array($this->astap?->spesifikasi_json)
            ? $this->astap->spesifikasi_json
            : (json_decode($this->astap?->spesifikasi_json ?? '', true) ?? []);
        return $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? '-'));
    }
}

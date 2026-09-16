<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapRegister extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Relasi ke rincian transaksi mutasi register.
     */
    public function mutasiRegisters()
    {
        return $this->hasMany(AstapMutasiRegister::class, 'astap_register_id');
    }

    /**
     * Relasi ke dokumen Berita Acara Mutasi (BAMB).
     */
    public function mutasis()
    {
        return $this->belongsToMany(AstapMutasi::class, 'astap_mutasi_registers', 'astap_register_id', 'astap_mutasi_id')
            ->where('astap_mutasis.is_deleted', 0)
            ->withPivot('kondisi')
            ->withTimestamps();
    }

    public function distribusiItemRegisters()
    {
        return $this->hasMany(DistribusiItemRegister::class);
    }

    public function getKode108Attribute(): string
    {
        return $this->astap ? ($this->astap->kode_108 ?? '') : '';
    }

    public function getQrCodePathAttribute($value): string
    {
        return $value ?: '/scan/' . ($this->nibar ?? $this->no_register ?? '');
    }
}

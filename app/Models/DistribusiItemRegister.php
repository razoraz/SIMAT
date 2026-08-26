<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistribusiItemRegister extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi ke baris item distribusi
     */
    public function distribusiItem()
    {
        return $this->belongsTo(DistribusiItem::class);
    }

    /**
     * Relasi ke unit fisik NIBAR (kondisi, ruang, nibar dibaca dari sini)
     */
    public function astapRegister()
    {
        return $this->belongsTo(AstapRegister::class);
    }

    // ── Accessor Shortcut (baca langsung dari relasi, tidak duplikasi data) ──

    public function getNibarAttribute(): string
    {
        return $this->astapRegister?->nibar ?? '-';
    }

    public function getKondisiAttribute(): string
    {
        return $this->astapRegister?->kondisi ?? 'Baik';
    }

    public function getRuangAttribute(): string
    {
        return $this->astapRegister?->ruang_pemegang ?? 'Gudang Aset';
    }
}

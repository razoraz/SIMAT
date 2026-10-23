<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapRegister extends Model
{
    use HasFactory, \App\Traits\TrackableSoftDelete;

    protected $guarded = ['id'];

    protected $casts = [
        'is_deleted' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($reg) {
            $specialStatuses = ['Mutasi Keluar OPD', 'Kemitraan', 'Dihibahkan', 'Dibatalkan'];
            if (!in_array($reg->status, $specialStatuses)) {
                $unplacedPlaceholders = [
                    '', '-', 'belum ditempatkan', 'belum ditempatkan / di gudang',
                    'belum ditempatkan / di gudang aset', 'gudang aset',
                    'gudang aset utama / belum ditempatkan', 'gudang perbekalan'
                ];
                $clean = strtolower(trim((string)$reg->ruang_pemegang));
                $isPlaced = !empty($reg->unit_id) || (!empty($clean) && !in_array($clean, $unplacedPlaceholders) && !str_starts_with($clean, 'kemitraan') && !str_starts_with($clean, 'telah dikembalikan'));

                if ($isPlaced) {
                    $reg->status = 'Tidak Tersedia';
                } else {
                    $reg->status = 'Tersedia';
                }
            }
        });
    }

    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Relasi ke rincian transaksi mutasi register internal.
     */
    public function mutasiRegisters()
    {
        return $this->hasMany(AstapMutasiRegister::class, 'astap_register_id');
    }

    /**
     * Relasi ke rincian transaksi mutasi eksternal register (antar-OPD).
     */
    public function mutasiEksternalRegisters()
    {
        return $this->hasMany(MutasiEksternalRegister::class, 'astap_register_id');
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

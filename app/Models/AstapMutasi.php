<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapMutasi extends Model
{
    use HasFactory, \App\Traits\TrackableSoftDelete;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_mutasi'           => 'date:d/m/Y',
        'tgl_persetujuan_pengirim' => 'datetime',
        'tgl_persetujuan_penerima' => 'datetime',
        'tgl_persetujuan_admin'    => 'datetime',
        'is_deleted'               => 'integer',
        'deleted_at'               => 'datetime',
    ];

    public function setTanggalMutasiAttribute($value)
    {
        $this->attributes['tanggal_mutasi'] = Astap::parseDateInput($value);
    }

    /**
     * Rincian item register yang dimutasi dalam Berita Acara ini.
     */
    public function items()
    {
        return $this->hasMany(AstapMutasiRegister::class, 'astap_mutasi_id');
    }

    /**
     * Relasi Many-to-Many ke master register unit aset.
     */
    public function registers()
    {
        return $this->belongsToMany(AstapRegister::class, 'astap_mutasi_registers', 'astap_mutasi_id', 'astap_register_id')
            ->withPivot('kondisi')
            ->withTimestamps();
    }

    /**
     * Backward-compatibility relasi single register.
     */
    public function register()
    {
        return $this->belongsTo(AstapRegister::class, 'astap_register_id');
    }

    /**
     * Helper bisnis untuk memeriksa apakah transaksi mutasi ini merupakan
     * pengembalian barang ke Pengurus Barang / Gudang / Depo.
     */
    public function isReturnToGudang(): bool
    {
        if ($this->jenis_mutasi !== 'Pengembalian') {
            return false;
        }

        $tujuan = strtolower(trim($this->ruangan_tujuan ?? ''));
        return str_contains($tujuan, 'perbekalan') ||
               str_contains($tujuan, 'rumah tangga') ||
               str_contains($tujuan, 'gudang') ||
               str_contains($tujuan, 'depo');
    }
}

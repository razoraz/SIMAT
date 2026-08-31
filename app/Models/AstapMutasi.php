<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapMutasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_mutasi'           => 'date',
        'tgl_persetujuan_pengirim' => 'datetime',
        'tgl_persetujuan_penerima' => 'datetime',
        'tgl_persetujuan_admin'    => 'datetime',
    ];

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
}

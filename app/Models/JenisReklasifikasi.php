<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisReklasifikasi extends Model
{
    use HasFactory;

    protected $table = 'jenis_reklasifikasis';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan'    => 'integer',
    ];

    /**
     * Scope untuk mengambil hanya baris aktif berurutan
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan', 'asc');
    }

    /**
     * Relasi ke transaksi reklasifikasi sebagai asal
     */
    public function reklasAsal()
    {
        return $this->hasMany(AstapReklas::class, 'jenis_reklasifikasi_asal_id');
    }

    /**
     * Relasi ke transaksi reklasifikasi sebagai tujuan
     */
    public function reklasTujuan()
    {
        return $this->hasMany(AstapReklas::class, 'jenis_reklasifikasi_tujuan_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPengadaan extends Model
{
    protected $fillable = [
        'program_kode',
        'program_nama',
        'kegiatan_kode',
        'kegiatan_nama',
        'sub_kegiatan_kode',
        'sub_kegiatan_nama',
    ];

    /**
     * Relasi ke data transaksi Belanja Modal Aset (AstapBelanjaModal)
     */
    public function astapBelanjaModals()
    {
        return $this->hasMany(\App\Models\AstapBelanjaModal::class, 'jenis_pengadaan_id');
    }
}

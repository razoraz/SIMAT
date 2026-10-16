<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Tabel legacy 'astap_pelimpahan_skpds' telah digabungkan ke 'mutasi_eksternals' (tipe = 'masuk').
 * Gunakan model MutasiEksternal untuk seluruh transaksi mutasi antar-OPD (masuk/keluar).
 */
class AstapPelimpahanSkpd extends Model
{
    protected $table = 'mutasi_eksternals';
    protected $guarded = ['id'];

    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }
}

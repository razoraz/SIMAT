<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapReklas extends Model
{
    use HasFactory;

    protected $table = 'astap_reklasis';

    protected $guarded = ['id'];

    protected $casts = [
        'nilai_reklas'   => 'decimal:2',
        'tanggal_reklas' => 'date',
        'triwulan'       => 'integer',
        'tahun'          => 'integer',
    ];

    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }

    public function jenisReklasAsal()
    {
        return $this->belongsTo(JenisReklasifikasi::class, 'jenis_reklasifikasi_asal_id');
    }

    public function jenisReklasTujuan()
    {
        return $this->belongsTo(JenisReklasifikasi::class, 'jenis_reklasifikasi_tujuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

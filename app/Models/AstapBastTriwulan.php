<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapBastTriwulan extends Model
{
    use HasFactory;

    protected $table = 'astap_bast_triwulans';

    protected $fillable = [
        'tahun',
        'triwulan',
        'nomor_surat',
        'tanggal_bast',
        'lokasi',
        'pihak1_nama',
        'pihak1_nip',
        'pihak1_jabatan',
        'pihak2_nama',
        'pihak2_nip',
        'pihak2_jabatan',
        'direktur_nama',
        'direktur_nip',
        'status',
        'signed',
        'tgl_signed',
        'qr_hash',
        'catatan',
    ];

    protected $casts = [
        'tanggal_bast' => 'date:d/m/Y',
        'signed'       => 'boolean',
    ];

    public function setTanggalBastAttribute($value)
    {
        $this->attributes['tanggal_bast'] = Astap::parseDateInput($value);
    }
}

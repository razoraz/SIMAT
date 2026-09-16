<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapBastTriwulan extends Model
{
    use HasFactory, \App\Traits\TrackableSoftDelete;

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
        'is_deleted',
        'deleted_by',
        'deleted_by_id',
        'deleted_at',
    ];

    protected $casts = [
        'tanggal_bast' => 'date',
        'signed'       => 'boolean',
        'is_deleted'   => 'integer',
        'deleted_at'   => 'datetime',
    ];

    protected $attributes = [
        'lokasi' => 'Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso',
        'status' => 'draft',
        'signed' => false,
    ];

    public function setTanggalBastAttribute($value)
    {
        $this->attributes['tanggal_bast'] = Astap::parseDateInput($value);
    }
}

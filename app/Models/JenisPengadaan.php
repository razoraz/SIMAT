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
}

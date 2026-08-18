<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisAstap extends Model
{
    protected $fillable = [
        'jenis',
        'nama_jenis',
        'sub_rincian_objek',
        'uraian_sub_rincian',
        'sub_sub_rincian_objek',
        'uraian_sub_sub_rincian',
    ];
}
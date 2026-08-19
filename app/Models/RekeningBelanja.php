<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningBelanja extends Model
{
    protected $fillable = [
        'kelompok',
        'nama_kelompok',
        'kode_rek',
        'nama_belanja',
    ];
}

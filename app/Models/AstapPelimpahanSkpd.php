<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapPelimpahanSkpd extends Model
{
    use HasFactory;

    protected $table = 'astap_pelimpahan_skpds';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_bamb'    => 'date:d/m/Y',
        'nilai_perolehan' => 'decimal:2',
    ];

    public function setTanggalBambAttribute($value)
    {
        $this->attributes['tanggal_bamb'] = Astap::parseDateInput($value);
    }

    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }
}

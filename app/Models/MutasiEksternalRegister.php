<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiEksternalRegister extends Model
{
    use HasFactory;

    protected $table = 'mutasi_eksternal_registers';

    protected $guarded = ['id'];

    /**
     * Relasi ke Header Mutasi Eksternal
     */
    public function mutasiEksternal()
    {
        return $this->belongsTo(MutasiEksternal::class, 'mutasi_eksternal_id');
    }

    /**
     * Relasi ke Register Unit NIBAR
     */
    public function register()
    {
        return $this->belongsTo(AstapRegister::class, 'astap_register_id');
    }
}

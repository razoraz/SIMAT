<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapMutasiRegister extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function mutasi()
    {
        return $this->belongsTo(AstapMutasi::class, 'astap_mutasi_id');
    }

    public function register()
    {
        return $this->belongsTo(AstapRegister::class, 'astap_register_id');
    }
}

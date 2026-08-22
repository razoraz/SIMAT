<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapMutasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_mutasi' => 'date',
    ];

    public function register()
    {
        return $this->belongsTo(AstapRegister::class, 'astap_register_id');
    }
}

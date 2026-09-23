<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapHibah extends Model
{
    use HasFactory;

    protected $table = 'astap_hibahs';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_bast' => 'date',
        'nilai_aset'   => 'decimal:2',
        'jumlah_volume'=> 'integer',
        'tahun'        => 'integer',
    ];

    public function astap()
    {
        return $this->belongsTo(Astap::class, 'astap_id');
    }

    public function register()
    {
        return $this->belongsTo(AstapRegister::class, 'astap_register_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

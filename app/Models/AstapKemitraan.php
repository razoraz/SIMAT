<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TrackableSoftDelete;
use Carbon\Carbon;

class AstapKemitraan extends Model
{
    use HasFactory, TrackableSoftDelete;

    protected $table = 'astap_kemitraans';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pks'     => 'date',
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'nilai_aset'      => 'decimal:2',
        'jumlah_volume'   => 'integer',
        'tahun'           => 'integer',
        'is_deleted'      => 'integer',
        'deleted_at'      => 'datetime',
    ];

    public function astap()
    {
        return $this->belongsTo(Astap::class, 'astap_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Hitung sisa hari konsesi kerjasama
     */
    public function getSisaHariKonsesiAttribute()
    {
        if (!$this->tanggal_selesai) {
            return null;
        }

        $now = Carbon::now()->startOfDay();
        $selesai = Carbon::parse($this->tanggal_selesai)->startOfDay();

        return (int) $now->diffInDays($selesai, false);
    }
}

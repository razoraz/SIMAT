<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TrackableSoftDelete;

class AstapBelanjaBarang extends Model
{
    use HasFactory, TrackableSoftDelete;

    protected $table = 'astap_belanja_barangs';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_faktur'  => 'date:d/m/Y',
        'total_pembelian' => 'decimal:2',
        'is_deleted'      => 'integer',
        'deleted_at'      => 'datetime',
    ];

    public function setTanggalFakturAttribute($value)
    {
        $this->attributes['tanggal_faktur'] = Astap::parseDateInput($value);
    }

    public function getNamaTokoAttribute()
    {
        return $this->toko_penyedia;
    }

    public function astap()
    {
        return $this->hasOne(Astap::class, 'belanja_barang_id');
    }

    public function astaps()
    {
        return $this->hasMany(Astap::class, 'belanja_barang_id');
    }

    public function items()
    {
        return $this->astaps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDokumenFakturUrlAttribute()
    {
        return $this->dokumen_faktur ? asset('storage/' . $this->dokumen_faktur) : null;
    }

    public function getDokumenKuitansiUrlAttribute()
    {
        return $this->dokumen_kuitansi ? asset('storage/' . $this->dokumen_kuitansi) : null;
    }

    public function getTotalVolumeAttribute()
    {
        return (int) $this->astaps->sum('jumlah_volume');
    }

    public function getTotalItemCountAttribute()
    {
        return $this->astaps->count();
    }
}

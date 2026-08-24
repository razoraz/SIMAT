<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_distribusi' => 'date',
        'signed'             => 'boolean',
    ];

    /**
     * Relasi ke Master Unit (Cukup simpan unit_id)
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Relasi ke Rincian Item Barang Distribusi
     */
    public function items()
    {
        return $this->hasMany(DistribusiItem::class);
    }

    /**
     * Accessor: Nama Unit / Ruangan Tujuan (Langsung dari relasi Unit)
     */
    public function getTujuanAttribute()
    {
        return $this->unit?->nama ?? '-';
    }

    /**
     * Accessor: Nama Pegawai / PJ Penerima (Langsung dari relasi Unit)
     */
    public function getPenerimaAttribute()
    {
        return $this->unit?->kepala ?? '-';
    }

    /**
     * Accessor: NIP Pegawai Penerima (Langsung dari relasi Unit)
     */
    public function getPenerimaNipAttribute()
    {
        return $this->unit?->nip ?? '-';
    }

    /**
     * Accessor: Jabatan Penerima
     */
    public function getPenerimaJabatanAttribute()
    {
        return $this->unit ? ('Kepala / Penanggung Jawab ' . $this->unit->nama) : '-';
    }

    /**
     * Accessor: Total Volume / Qty Barang
     */
    public function getTotalQtyAttribute()
    {
        return $this->items->sum('qty');
    }
}

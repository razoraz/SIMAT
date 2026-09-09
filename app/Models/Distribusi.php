<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_distribusi' => 'date:d/m/Y',
        'signed'             => 'boolean',
    ];

    public function setTanggalDistribusiAttribute($value)
    {
        $this->attributes['tanggal_distribusi'] = Astap::parseDateInput($value);
    }

    /**
     * Listener saat Distribusi dihapus: otomatis reset unit_id, ruang_pemegang, dan status register NIBAR
     */
    protected static function booted()
    {
        static::deleting(function ($distribusi) {
            foreach ($distribusi->items as $item) {
                $regIds = $item->registers->pluck('astap_register_id')->filter()->toArray();
                if (!empty($regIds)) {
                    AstapRegister::whereIn('id', $regIds)->update([
                        'unit_id'        => null,
                        'ruang_pemegang' => null,
                        'status'         => 'Tersedia',
                    ]);
                }
            }
        });
    }

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

    /**
     * Otomatis sinkronisasi status: Jika ada minimal 1 NIBAR yang diinputkan/di-ACC,
     * status otomatis berubah menjadi 'Dalam Pengiriman' dan nomor BAST resmi diterbitkan.
     */
    public function syncStatusWithNibar(): bool
    {
        if (in_array($this->status, ['Ditolak', 'Telah Diterima', 'Diterima'])) {
            return false;
        }

        $hasNibar = $this->items->contains(function ($it) {
            return ($it->registers && $it->registers->isNotEmpty()) || ((int)($it->qty_acc ?? 0) > 0);
        });

        if ($hasNibar && in_array($this->status, ['Menunggu Konfirmasi', 'Draft', 'Pending'])) {
            $this->status = 'Dalam Pengiriman';
            $tahunDist = $this->tanggal_distribusi ? date('Y', strtotime($this->tanggal_distribusi)) : date('Y');
            if (empty($this->bast_nomor) || str_contains($this->bast_nomor, 'Menunggu') || str_contains($this->bast_nomor, 'tidak')) {
                $this->bast_nomor = \App\Http\Controllers\DistribusiController::generateNextBastNomor((int)$tahunDist, $this->id);
            }
            $this->save();
            return true;
        }

        return false;
    }
}


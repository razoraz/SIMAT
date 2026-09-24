<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TrackableSoftDelete;

class MutasiEksternal extends Model
{
    use HasFactory, TrackableSoftDelete;

    protected $table = 'mutasi_eksternals';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_mutasi'       => 'date',
        'tgl_estimasi_kembali' => 'date',
        'nilai_perolehan'      => 'decimal:2',
        'jumlah_volume'        => 'integer',
        'is_deleted'           => 'integer',
        'deleted_at'           => 'datetime',
    ];

    /**
     * Mutator agar tanggal mutasi otomatis diparsing dari berbagai format input (dd/mm/yyyy, yyyy-mm-dd)
     */
    public function setTanggalMutasiAttribute($value)
    {
        $this->attributes['tanggal_mutasi'] = Astap::parseDateInput($value);
    }

    /**
     * Mutator tanggal estimasi pengembalian jika peminjaman antar-OPD
     */
    public function setTglEstimasiKembaliAttribute($value)
    {
        $this->attributes['tgl_estimasi_kembali'] = Astap::parseDateInput($value);
    }

    /**
     * Format nilai perolehan ke format Rupiah
     */
    public function getNilaiPerolehanFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->nilai_perolehan, 0, ',', '.');
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    public function scopeMasuk($query)
    {
        return $query->where('tipe', 'masuk');
    }

    public function scopeKeluar($query)
    {
        return $query->where('tipe', 'keluar');
    }

    // ─── Relations ──────────────────────────────────────────────────────────

    /**
     * Relasi ke master aset induk ASTAP
     */
    public function astap()
    {
        return $this->belongsTo(Astap::class, 'astap_id');
    }

    /**
     * Relasi ke Unit / Ruangan Penempatan di RSUD
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relasi ke User pencatat mutasi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper untuk mengambil seluruh unit register (NIBAR) terkait dari aset induk
     */
    public function getRegistersAttribute()
    {
        return $this->astap?->registers ?? collect();
    }
}

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
        'signed'               => 'boolean',
        'is_deleted'           => 'integer',
        'deleted_at'           => 'datetime',
        'dibatalkan_at'        => 'datetime',
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

    public function scopeDisahkan($query)
    {
        return $query->where('status', '!=', 'Dibatalkan');
    }

    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'Dibatalkan');
    }

    // ─── Relations ──────────────────────────────────────────────────────────

    /**
     * Relasi ke rincian item register yang dimutasi (khususnya mutasi keluar)
     */
    public function mutasiRegisters()
    {
        return $this->hasMany(MutasiEksternalRegister::class, 'mutasi_eksternal_id');
    }

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

    /**
     * Backward-compatibility alias attributes for legacy code
     */
    public function getSkpdAsalAttribute()
    {
        return $this->opd_asal;
    }

    public function getTanggalBambAttribute()
    {
        return $this->tanggal_mutasi ? $this->tanggal_mutasi->format('Y-m-d') : null;
    }

    public function getKeteranganAttribute()
    {
        return $this->alasan_mutasi;
    }
}

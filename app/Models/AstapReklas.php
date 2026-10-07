<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TrackableSoftDelete;

class AstapReklas extends Model
{
    use HasFactory, TrackableSoftDelete;

    protected $table = 'astap_reklasis';

    protected $guarded = ['id'];

    protected $casts = [
        'nilai_reklas'     => 'decimal:2',
        'tanggal_reklas'   => 'date',
        'triwulan'         => 'integer',
        'tahun'            => 'integer',
        'spesifikasi_lama' => 'array',
        'spesifikasi_baru' => 'array',
        'is_deleted'       => 'integer',
        'deleted_at'       => 'datetime',
    ];


    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }

    public function jenisReklasAsal()
    {
        return $this->belongsTo(JenisReklasifikasi::class, 'jenis_reklasifikasi_asal_id');
    }

    public function jenisReklasTujuan()
    {
        return $this->belongsTo(JenisReklasifikasi::class, 'jenis_reklasifikasi_tujuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk sub_koreksi dengan fallback backward-compatibility
     */
    public function getSubKoreksiAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        if ($this->jenis_reklas === 'KOREKSI_LAIN') {
            $info = $this->spesifikasi_baru['koreksi_info'] ?? null;
            if (!empty($info['sub_koreksi'])) {
                return $info['sub_koreksi'];
            }

            $haystack = strtolower(($this->keterangan ?? '') . ' ' . ($this->nomor_ba_reklas ?? '') . ' ' . ($this->alasan_reklas ?? ''));
            if (str_contains($haystack, 'manset')) {
                return 'manset';
            }
            if (str_contains($haystack, 'bpk') || str_contains($haystack, 'lkd') || str_contains($haystack, 'lhp')) {
                return 'lkd';
            }
            return 'biasa';
        }

        return null;
    }

    /**
     * Label resmi sub_koreksi untuk UI & Laporan RMB
     */
    public function getSubKoreksiLabelAttribute(): ?string
    {
        return match ($this->sub_koreksi) {
            'lkd'    => 'Koreksi LKD (BPK RI)',
            'manset' => 'Koreksi Manset (BPKAD)',
            'biasa'  => 'Koreksi Biasa (Internal)',
            default  => null,
        };
    }

    /**
     * Accessor arah koreksi (tambah / kurang)
     */
    public function getTipeKoreksiAttribute(): string
    {
        $info = $this->spesifikasi_baru['koreksi_info'] ?? null;
        if (!empty($info['tipe_koreksi'])) {
            return $info['tipe_koreksi'];
        }

        if ($this->asal_kode === 'KOR_LAIN' || $this->asal_kib === 'KOREKSI') {
            return 'tambah';
        }

        $ket = strtolower(($this->keterangan ?? '') . ' ' . ($this->nomor_ba_reklas ?? '') . ' ' . ($this->alasan_reklas ?? ''));
        if (str_contains($ket, 'penambahan') || str_contains($ket, 'bertambah') || str_contains($ket, 'kapitalisasi susulan')) {
            return 'tambah';
        }

        return 'kurang';
    }

    /**
     * Nilai buku sebelum koreksi
     */
    public function getNilaiSemulaAttribute(): float
    {
        $info = $this->spesifikasi_baru['koreksi_info'] ?? null;
        if (isset($info['nilai_semula'])) {
            return (float) $info['nilai_semula'];
        }

        $nilaiReklas = (float) $this->nilai_reklas;
        $totalAset = (float) ($this->astap?->total_realisasi ?? 0);

        return $this->tipe_koreksi === 'tambah'
            ? max(0, $totalAset - $nilaiReklas)
            : ($totalAset + $nilaiReklas);
    }

    /**
     * Nilai buku setelah koreksi
     */
    public function getNilaiSetelahKoreksiAttribute(): float
    {
        $info = $this->spesifikasi_baru['koreksi_info'] ?? null;
        if (isset($info['nilai_baru'])) {
            return (float) $info['nilai_baru'];
        }

        return (float) ($this->astap?->total_realisasi ?? 0);
    }

    /**
     * Pemetaan dampak kolom RMB resmi BPKAD 21 Kolom
     */
    public function getDampakRmbAttribute(): array
    {
        $sub = $this->sub_koreksi ?? 'biasa';
        $tipe = $this->tipe_koreksi;

        if ($sub === 'lkd') {
            return $tipe === 'tambah'
                ? ['kolom' => 6, 'label' => 'Kolom 6 (Koreksi LKD Bertambah)', 'kolom_text' => 'Kolom 6', 'badge' => 'cyan', 'arah' => '+']
                : ['kolom' => 16, 'label' => 'Kolom 16 (Koreksi LKD Berkurang)', 'kolom_text' => 'Kolom 16', 'badge' => 'cyan', 'arah' => '-'];
        }

        if ($sub === 'manset') {
            return $tipe === 'tambah'
                ? ['kolom' => 7, 'label' => 'Kolom 7 (Koreksi Manset Bertambah)', 'kolom_text' => 'Kolom 7', 'badge' => 'emerald', 'arah' => '+']
                : ['kolom' => 17, 'label' => 'Kolom 17 (Koreksi Manset Berkurang)', 'kolom_text' => 'Kolom 17', 'badge' => 'emerald', 'arah' => '-'];
        }

        return $tipe === 'tambah'
            ? ['kolom' => 5, 'label' => 'Kolom 5 (Koreksi Rek Bertambah)', 'kolom_text' => 'Kolom 5', 'badge' => 'indigo', 'arah' => '+']
            : ['kolom' => 15, 'label' => 'Kolom 15 (Koreksi Berkurang)', 'kolom_text' => 'Kolom 15', 'badge' => 'indigo', 'arah' => '-'];
    }

    /**
     * Scope query untuk transaksi koreksi nilai (KOREKSI_LAIN)
     */
    public function scopeKoreksiLain($query)
    {
        return $query->where('jenis_reklas', 'KOREKSI_LAIN');
    }
}

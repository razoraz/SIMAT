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

    public function objekAstap()
    {
        return $this->belongsTo(Astap::class, 'objek_astap_id');
    }

    public function objekRegister()
    {
        return $this->belongsTo(AstapRegister::class, 'objek_register_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function setTanggalPksAttribute($value)
    {
        $this->attributes['tanggal_pks'] = Astap::parseDateInput($value);
    }

    public function setTanggalMulaiAttribute($value)
    {
        $this->attributes['tanggal_mulai'] = Astap::parseDateInput($value);
    }

    public function setTanggalSelesaiAttribute($value)
    {
        $this->attributes['tanggal_selesai'] = Astap::parseDateInput($value);
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

    /**
     * Ambil seluruh riwayat mitra / rekanan pihak ketiga unik beserta nama pimpinan dan alamat
     */
    public static function getDistinctMitras()
    {
        try {
            $mitras = self::whereNotNull('mitra_nama')
                ->where('mitra_nama', '!=', '')
                ->where('is_deleted', 0)
                ->orderBy('id', 'desc')
                ->get(['id', 'mitra_nama', 'mitra_pimpinan', 'mitra_alamat', 'created_at'])
                ->groupBy(fn($item) => strtolower(trim($item->mitra_nama)))
                ->map(function ($group) {
                    $latest = $group->first();
                    $companyName = trim($latest->mitra_nama);

                    $historyPejabat = $group
                        ->map(fn($it) => trim($it->mitra_pimpinan ?? ''))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $historyAlamat = $group
                        ->map(fn($it) => trim($it->mitra_alamat ?? ''))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    return [
                        'nama'             => $companyName,
                        'pimpinan'         => $historyPejabat[0] ?? '',
                        'alamat'           => $historyAlamat[0] ?? '',
                        'history_pimpinan' => $historyPejabat,
                        'history_alamat'   => $historyAlamat,
                    ];
                })
                ->values();

            $knownNames = $mitras->pluck('nama')->map(fn($n) => strtolower(trim($n)))->toArray();
            $penyedias = \App\Models\AstapBelanjaModal::whereNotNull('penyedia_nama')
                ->where('penyedia_nama', '!=', '')
                ->orderBy('id', 'desc')
                ->get(['penyedia_nama', 'penyedia_pemilik', 'penyedia_alamat'])
                ->filter(fn($p) => strlen(trim($p->penyedia_nama)) >= 3 && !in_array(strtolower(trim($p->penyedia_nama)), $knownNames))
                ->groupBy(fn($p) => strtolower(trim($p->penyedia_nama)))
                ->map(function ($group) {
                    $first = $group->first();
                    return [
                        'nama'             => trim($first->penyedia_nama),
                        'pimpinan'         => trim($first->penyedia_pemilik ?? ''),
                        'alamat'           => trim($first->penyedia_alamat ?? ''),
                        'history_pimpinan' => array_filter([trim($first->penyedia_pemilik ?? '')]),
                        'history_alamat'   => array_filter([trim($first->penyedia_alamat ?? '')]),
                    ];
                })
                ->values();

            return $mitras->concat($penyedias)->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}

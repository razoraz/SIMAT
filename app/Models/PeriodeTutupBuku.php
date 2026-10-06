<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeTutupBuku extends Model
{
    use HasFactory;

    protected $table = 'periode_tutup_bukus';

    protected $guarded = ['id'];

    protected $casts = [
        'tahun'         => 'integer',
        'triwulan'      => 'integer',
        'is_locked'     => 'boolean',
        'tanggal_tutup' => 'date',
        'locked_at'     => 'datetime',
        'unlocked_at'   => 'datetime',
    ];

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function unlockedByUser()
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }

    /**
     * Cek apakah periode (tahun & triwulan) berstatus terkunci (locked).
     * Jika tutup buku tahunan (triwulan = 0) terkunci, maka seluruh triwulan di tahun tersebut otomatis terkunci.
     */
    public static function isLocked(int $tahun, ?int $triwulan = null): bool
    {
        // 1. Cek kunci tahunan menyeluruh (triwulan = 0)
        $annualLock = static::where('tahun', $tahun)
            ->where('triwulan', 0)
            ->where('is_locked', true)
            ->exists();

        if ($annualLock) {
            return true;
        }

        // 2. Cek kunci spesifik triwulan jika ditentukan (1-4)
        if ($triwulan !== null && $triwulan >= 1 && $triwulan <= 4) {
            return static::where('tahun', $tahun)
                ->where('triwulan', $triwulan)
                ->where('is_locked', true)
                ->exists();
        }

        return false;
    }

    /**
     * Dapatkan data lengkap status penguncian periode.
     */
    public static function getLockInfo(int $tahun, ?int $triwulan = null): ?self
    {
        // Prioritas cek kunci tahunan
        $annual = static::where('tahun', $tahun)
            ->where('triwulan', 0)
            ->where('is_locked', true)
            ->first();

        if ($annual) {
            return $annual;
        }

        if ($triwulan !== null) {
            return static::where('tahun', $tahun)
                ->where('triwulan', $triwulan)
                ->where('is_locked', true)
                ->first();
        }

        return null;
    }

    /**
     * Label nama triwulan / periode
     */
    public function getNamaPeriodeAttribute(): string
    {
        return match ($this->triwulan) {
            1 => 'Triwulan 1 (Januari - Maret)',
            2 => 'Triwulan 2 (April - Juni)',
            3 => 'Triwulan 3 (Juli - September)',
            4 => 'Triwulan 4 (Oktober - Desember)',
            0 => 'Tutup Buku Tahunan (31 Desember)',
            default => 'Triwulan ' . $this->triwulan,
        };
    }

    /**
     * Parse input triwulan berbagai format ('TW I', 'TW 1', 'Triwulan 1', 1) ke integer (1-4 atau 0).
     */
    public static function parseTriwulan($val): int
    {
        if (is_numeric($val)) {
            $num = (int) $val;
            return ($num >= 0 && $num <= 4) ? $num : 1;
        }

        $str = strtoupper(trim((string) $val));
        if (str_contains($str, 'IV') || str_contains($str, '4')) return 4;
        if (str_contains($str, 'III') || str_contains($str, '3')) return 3;
        if (str_contains($str, 'II') || str_contains($str, '2')) return 2;
        if (str_contains($str, 'I') || str_contains($str, '1')) return 1;
        if (str_contains($str, 'TAHUN') || str_contains($str, '0')) return 0;

        return 1;
    }
}

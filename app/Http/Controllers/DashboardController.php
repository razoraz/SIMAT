<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\JenisAstap;
use App\Models\Unit;
use App\Models\Distribusi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard Master Admin
     */
    public function masterAdmin()
    {
        $data = $this->getDashboardData();
        return view('dashboards.master_admin', $data);
    }

    /**
     * Dashboard Admin Operasional
     */
    public function admin()
    {
        $data = $this->getDashboardData();
        return view('dashboards.admin', $data);
    }

    /**
     * Helper untuk menghitung data statistik & data tabel ASTAP riil dari DB SQLite
     */
    private function getDashboardData(): array
    {
        // 1. Card 1: Total Keseluruhan Volume Aset ASTAP & Master ASTAP
        $totalAsetVolumeCount   = (int) Astap::sum('jumlah_volume');
        $totalAsetRegisterCount = AstapRegister::count();
        $totalAstapMasterCount  = Astap::count();

        // 2. Card 2: Harga Aset Keseluruhan (Total Rupiah Dana Aset RSUD)
        $totalValuasi = (float) Astap::sum('total_realisasi');
        if ($totalValuasi >= 1000000000) {
            $hargaAsetFormatted = 'Rp ' . number_format($totalValuasi / 1000000000, 2, ',', '.');
            $hargaAsetUnit = 'Miliar';
        } elseif ($totalValuasi >= 1000000) {
            $hargaAsetFormatted = 'Rp ' . number_format($totalValuasi / 1000000, 2, ',', '.');
            $hargaAsetUnit = 'Juta';
        } else {
            $hargaAsetFormatted = 'Rp ' . number_format($totalValuasi, 0, ',', '.');
            $hargaAsetUnit = '';
        }
        $hargaAsetFull = 'Rp ' . number_format($totalValuasi, 0, ',', '.');

        // 3. Card 3: Jumlah Unit RSUD & Distribusi Barang
        $totalUnitRsudCount       = Unit::count();
        $totalTerdistribusiUnit   = AstapRegister::whereNotNull('unit_id')->count();
        $totalTransaksiDistribusi = Distribusi::count();

        // 4. Card 4: Kondisi Aset
        $kondisiBaik         = AstapRegister::where('kondisi', 'Baik')->count();
        $kondisiKurangBaik   = AstapRegister::where('kondisi', 'Kurang Baik')->count();
        $kondisiRusakRingan  = AstapRegister::where('kondisi', 'Rusak Ringan')->count();
        $kondisiRusakBerat   = AstapRegister::whereIn('kondisi', ['Rusak Berat', 'Rusak'])->count();
        $totalRusak          = $kondisiKurangBaik + $kondisiRusakRingan + $kondisiRusakBerat;

        // 5. Data Grafik Peningkatan Aset (Harga & Kuantitas per Tahun)
        $yearlyStats = Astap::selectRaw('tahun_perolehan, SUM(jumlah_volume) as total_volume, SUM(total_realisasi) as total_harga')
            ->whereNotNull('tahun_perolehan')
            ->where('tahun_perolehan', '>', 0)
            ->groupBy('tahun_perolehan')
            ->orderBy('tahun_perolehan', 'asc')
            ->get();

        $chartLabels = [];
        $chartVolumeData = [];
        $chartHargaDataJuta = [];
        $chartKumulatifVolume = [];
        $chartKumulatifHargaJuta = [];

        $runningVolume = 0;
        $runningHarga = 0;

        foreach ($yearlyStats as $stat) {
            $year = (string) $stat->tahun_perolehan;
            $vol = (int) $stat->total_volume;
            $harga = (float) $stat->total_harga;
            $hargaJuta = round($harga / 1000000, 2);

            $runningVolume += $vol;
            $runningHarga += $harga;
            $runningHargaJuta = round($runningHarga / 1000000, 2);

            $chartLabels[] = 'Thn ' . $year;
            $chartVolumeData[] = $vol;
            $chartHargaDataJuta[] = $hargaJuta;
            $chartKumulatifVolume[] = $runningVolume;
            $chartKumulatifHargaJuta[] = $runningHargaJuta;
        }

        // 6. Data ASTAP Terbaru dari SQLite DB
        $recentAstaps = Astap::with(['jenisAstap', 'registers.unit'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($a) {
                $firstReg = $a->registers ? $a->registers->first() : null;
                
                // Cari lokasi unit dari registers
                $lokasiUnits = $a->registers ? $a->registers->map(function($r) {
                    return $r->unit?->nama ?: ($r->ruang_pemegang ?: null);
                })->filter()->unique()->values()->all() : [];

                $lokasiStr = !empty($lokasiUnits) 
                    ? implode(', ', array_slice($lokasiUnits, 0, 2)) 
                    : 'Gudang Aset';

                if (count($lokasiUnits) > 2) {
                    $lokasiStr .= ' (+' . (count($lokasiUnits) - 2) . ' lokasi)';
                }

                $jenisNama = $a->jenisAstap 
                    ? ($a->jenisAstap->nama_jenis ?: ($a->jenisAstap->uraian_sub_sub_rincian ?: 'PERALATAN DAN MESIN'))
                    : ($a->category === 'ATB' ? 'ASET TAK BERWUJUD' : 'PERALATAN DAN MESIN');

                return [
                    'id'          => $a->id,
                    'kode_barang' => $a->kode_108 ?: ('AST-' . str_pad($a->id, 3, '0', STR_PAD_LEFT)),
                    'nama_barang' => $a->nama_barang,
                    'jenis_nama'  => strtoupper($jenisNama),
                    'tahun'       => (string) $a->tahun_perolehan,
                    'volume'      => $a->jumlah_volume . ' ' . ($a->satuan ?: 'Unit'),
                    'lokasi'      => $lokasiStr,
                    'kondisi'     => $firstReg ? $firstReg->kondisi : 'Baik',
                ];
            });

        // 7. Data Grafik Distribusi per Unit/Ruangan Terbanyak
        $topUnitsDist = AstapRegister::whereNotNull('unit_id')
            ->join('units', 'astap_registers.unit_id', '=', 'units.id')
            ->selectRaw('units.nama as unit_nama, count(*) as total')
            ->groupBy('units.id', 'units.nama')
            ->orderBy('total', 'desc')
            ->limit(6)
            ->get();

        $chartTopUnitLabels = [];
        $chartTopUnitData = [];
        foreach ($topUnitsDist as $u) {
            $chartTopUnitLabels[] = $u->unit_nama;
            $chartTopUnitData[]   = (int) $u->total;
        }

        $belumTerdistribusi = max(0, $totalAsetRegisterCount - $totalTerdistribusiUnit);
        $persenTerdistribusi = $totalAsetRegisterCount > 0 ? round(($totalTerdistribusiUnit / $totalAsetRegisterCount) * 100, 1) : 0;
        $persenBaik = $totalAsetRegisterCount > 0 ? round(($kondisiBaik / $totalAsetRegisterCount) * 100, 1) : 0;

        return [
            'totalAsetVolumeCount'     => $totalAsetVolumeCount,
            'totalAsetRegisterCount'   => $totalAsetRegisterCount,
            'totalAstapMasterCount'    => $totalAstapMasterCount,
            'totalValuasi'             => $totalValuasi,
            'hargaAsetFormatted'       => $hargaAsetFormatted,
            'hargaAsetUnit'            => $hargaAsetUnit,
            'hargaAsetFull'            => $hargaAsetFull,
            'totalUnitRsudCount'       => $totalUnitRsudCount,
            'totalTerdistribusiUnit'   => $totalTerdistribusiUnit,
            'totalTransaksiDistribusi' => $totalTransaksiDistribusi,
            'belumTerdistribusi'       => $belumTerdistribusi,
            'persenTerdistribusi'      => $persenTerdistribusi,
            'persenBaik'               => $persenBaik,
            'kondisiBaik'              => $kondisiBaik,
            'kondisiKurangBaik'        => $kondisiKurangBaik,
            'kondisiRusakRingan'       => $kondisiRusakRingan,
            'kondisiRusakBerat'        => $kondisiRusakBerat,
            'totalRusak'               => $totalRusak,
            'recentAstaps'             => $recentAstaps,
            'chartLabels'              => $chartLabels,
            'chartVolumeData'          => $chartVolumeData,
            'chartHargaDataJuta'       => $chartHargaDataJuta,
            'chartKumulatifVolume'     => $chartKumulatifVolume,
            'chartKumulatifHargaJuta'  => $chartKumulatifHargaJuta,
            'chartKondisiLabels'       => ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'],
            'chartKondisiData'         => [$kondisiBaik, $kondisiKurangBaik, $kondisiRusakRingan, $kondisiRusakBerat],
            'chartDistribusiStatusLabels' => ['Terdistribusi ke Ruangan', 'Belum Didistribusi (Gudang)'],
            'chartDistribusiStatusData'   => [$totalTerdistribusiUnit, $belumTerdistribusi],
            'chartTopUnitLabels'       => $chartTopUnitLabels,
            'chartTopUnitData'         => $chartTopUnitData,
        ];
    }
}

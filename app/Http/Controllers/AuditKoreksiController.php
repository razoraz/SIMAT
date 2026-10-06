<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapReklas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditKoreksiController extends Controller
{
    /**
     * Halaman Dashboard & Ledger Audit Koreksi Nilai BMD (Biasa, LKD, Manset)
     */
    public function index(Request $request)
    {
        $selectedYear = (int) ($request->input('tahun', date('Y')));
        $selectedTriwulan = $request->input('triwulan', 'all');
        $selectedSubKoreksi = $request->input('sub_koreksi', 'all');
        $selectedTipe = $request->input('tipe_koreksi', 'all');
        $search = trim((string) $request->input('search', ''));

        // Ambil daftar tahun unik yang tersedia di database
        $availableYears = AstapReklas::koreksiLain()
            ->distinct()
            ->pluck('tahun')
            ->filter()
            ->sortDesc()
            ->values()
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        if (!in_array($selectedYear, $availableYears) && $request->has('tahun')) {
            $selectedYear = $availableYears[0];
        }

        // Query Dasar untuk Statistik Global Periode Terpilih
        $baseQuery = AstapReklas::koreksiLain()
            ->with(['astap.jenisAstap', 'astap.rekeningBelanja', 'astap.unit', 'user']);

        if ($selectedYear !== 'all' && $selectedYear > 0) {
            $baseQuery->where('tahun', $selectedYear);
        }

        if ($selectedTriwulan !== 'all' && in_array((int) $selectedTriwulan, [1, 2, 3, 4])) {
            $baseQuery->where('triwulan', (int) $selectedTriwulan);
        }

        // Ambil data untuk statistik periode
        $periodRecords = $baseQuery->get();

        // Hitung KPI / Statistik komprehensif
        $stats = $this->calculateStatistics($periodRecords);

        // Filter lanjutan untuk tampilan data (Search & Sub Koreksi & Tipe)
        $filteredQuery = clone $baseQuery;

        if ($search !== '') {
            $filteredQuery->where(function ($q) use ($search) {
                $q->where('nomor_ba_reklas', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('alasan_reklas', 'like', "%{$search}%")
                    ->orWhere('asal_nama', 'like', "%{$search}%")
                    ->orWhere('tujuan_nama', 'like', "%{$search}%")
                    ->orWhereHas('astap', function ($qa) use ($search) {
                        $qa->where('nama_barang', 'like', "%{$search}%")
                            ->orWhere('nibar', 'like', "%{$search}%")
                            ->orWhere('spesifikasi_nama_barang', 'like', "%{$search}%")
                            ->orWhere('register_kode', 'like', "%{$search}%");
                    });
            });
        }

        $allFiltered = $filteredQuery->orderBy('tanggal_reklas', 'desc')->orderBy('id', 'desc')->get();

        // Terapkan filter sub_koreksi dan tipe_koreksi jika ada
        $viewRecords = $allFiltered;
        if ($selectedSubKoreksi !== 'all') {
            $viewRecords = $viewRecords->filter(fn($item) => $item->sub_koreksi === $selectedSubKoreksi);
        }
        if ($selectedTipe !== 'all') {
            $viewRecords = $viewRecords->filter(fn($item) => $item->tipe_koreksi === $selectedTipe);
        }
        $viewRecords = $viewRecords->values();

        // Pembagian koleksi untuk masing-masing tab khusus
        $semuaKoreksi  = $allFiltered;
        $biasaKoreksi  = $allFiltered->filter(fn($item) => $item->sub_koreksi === 'biasa')->values();
        $lkdKoreksi    = $allFiltered->filter(fn($item) => $item->sub_koreksi === 'lkd')->values();
        $mansetKoreksi = $allFiltered->filter(fn($item) => $item->sub_koreksi === 'manset')->values();

        return view('pages.audit_koreksi.index', compact(
            'viewRecords',
            'semuaKoreksi',
            'biasaKoreksi',
            'lkdKoreksi',
            'mansetKoreksi',
            'stats',
            'availableYears',
            'selectedYear',
            'selectedTriwulan',
            'selectedSubKoreksi',
            'selectedTipe',
            'search'
        ));
    }

    /**
     * Hitung metrik dan dampak neraca RMB untuk masing-masing sub-koreksi
     */
    protected function calculateStatistics($records): array
    {
        $stats = [
            'total_count'       => $records->count(),
            'total_tambah'      => 0.0,
            'total_kurang'      => 0.0,
            'net_koreksi'       => 0.0,
            // Koreksi Biasa (Internal)
            'biasa' => [
                'count'        => 0,
                'tambah'       => 0.0, // Kolom 5 RMB
                'kurang'       => 0.0, // Kolom 15 RMB
                'net'          => 0.0,
                'rmb_tambah'   => 'Kolom 5 (Koreksi Rek Bertambah)',
                'rmb_kurang'   => 'Kolom 15 (Koreksi Rek Berkurang)',
            ],
            // Koreksi LKD (BPK RI)
            'lkd' => [
                'count'        => 0,
                'tambah'       => 0.0, // Kolom 6 RMB
                'kurang'       => 0.0, // Kolom 16 RMB
                'net'          => 0.0,
                'rmb_tambah'   => 'Kolom 6 (Koreksi LKD Bertambah)',
                'rmb_kurang'   => 'Kolom 16 (Koreksi LKD Berkurang)',
            ],
            // Koreksi Manset (BPKAD)
            'manset' => [
                'count'        => 0,
                'tambah'       => 0.0, // Kolom 7 RMB
                'kurang'       => 0.0, // Kolom 17 RMB
                'net'          => 0.0,
                'rmb_tambah'   => 'Kolom 7 (Koreksi Manset Bertambah)',
                'rmb_kurang'   => 'Kolom 17 (Koreksi Manset Berkurang)',
            ],
        ];

        foreach ($records as $item) {
            $nominal = (float) $item->nilai_reklas;
            $sub = $item->sub_koreksi ?? 'biasa';
            $tipe = $item->tipe_koreksi;

            if ($tipe === 'tambah') {
                $stats['total_tambah'] += $nominal;
            } else {
                $stats['total_kurang'] += $nominal;
            }

            if (isset($stats[$sub])) {
                $stats[$sub]['count']++;
                if ($tipe === 'tambah') {
                    $stats[$sub]['tambah'] += $nominal;
                } else {
                    $stats[$sub]['kurang'] += $nominal;
                }
            }
        }

        $stats['net_koreksi'] = $stats['total_tambah'] - $stats['total_kurang'];
        $stats['biasa']['net'] = $stats['biasa']['tambah'] - $stats['biasa']['kurang'];
        $stats['lkd']['net'] = $stats['lkd']['tambah'] - $stats['lkd']['kurang'];
        $stats['manset']['net'] = $stats['manset']['tambah'] - $stats['manset']['kurang'];

        return $stats;
    }

    /**
     * API JSON Detail Transaksi Koreksi Nilai untuk Modal Audit Trail
     */
    public function show($id)
    {
        $koreksi = AstapReklas::koreksiLain()
            ->with(['astap.jenisAstap', 'astap.rekeningBelanja', 'astap.unit', 'user'])
            ->findOrFail($id);

        $specBaru = $koreksi->spesifikasi_baru ?? [];
        $koreksiInfo = $specBaru['koreksi_info'] ?? null;

        $payload = [
            'id'                     => $koreksi->id,
            'astap_id'               => $koreksi->astap_id,
            'nibar'                  => $koreksi->astap?->nibar ?: '-',
            'nama_barang'            => $koreksi->astap?->nama_barang ?: 'Aset Tidak Ditemukan',
            'kelompok_kib'           => $koreksi->astap?->category ?: ($koreksi->asal_kib ?: 'KIB B'),
            'kode_108'               => $koreksi->astap?->kode_108 ?: ($koreksi->astap?->jenisAstap?->sub_sub_rincian_objek ?: '-'),
            'rekening_belanja'       => $koreksi->astap?->rekeningBelanja?->nama_rekening ?: ($koreksi->asal_nama ?: '-'),
            'sub_koreksi'            => $koreksi->sub_koreksi,
            'sub_koreksi_label'      => $koreksi->sub_koreksi_label,
            'tipe_koreksi'           => $koreksi->tipe_koreksi,
            'nilai_reklas'           => (float) $koreksi->nilai_reklas,
            'nilai_semula'           => $koreksi->nilai_semula,
            'nilai_setelah_koreksi'  => $koreksi->nilai_setelah_koreksi,
            'tanggal_reklas'         => $koreksi->tanggal_reklas?->format('d/m/Y') ?: '-',
            'triwulan'               => $koreksi->triwulan,
            'tahun'                  => $koreksi->tahun,
            'nomor_ba_reklas'        => $koreksi->nomor_ba_reklas ?: '-',
            'alasan_reklas'          => $koreksi->alasan_reklas ?: ($koreksi->keterangan ?: '-'),
            'keterangan'             => $koreksi->keterangan ?: '-',
            'dampak_rmb'             => $koreksi->dampak_rmb,
            'user_nama'              => $koreksi->user?->name ?: 'Administrator SIMAT',
            'created_at'             => $koreksi->created_at?->format('d/m/Y H:i') ?: '-',
            'koreksi_info'           => $koreksiInfo,
        ];

        return response()->json([
            'success' => true,
            'data'    => $payload,
        ]);
    }

    /**
     * Ekspor Data Rekapitulasi Audit Koreksi Nilai ke Format CSV Resmi
     */
    public function export(Request $request): StreamedResponse
    {
        $selectedYear = (int) ($request->input('tahun', date('Y')));
        $selectedTriwulan = $request->input('triwulan', 'all');
        $selectedSubKoreksi = $request->input('sub_koreksi', 'all');
        $selectedTipe = $request->input('tipe_koreksi', 'all');

        $query = AstapReklas::koreksiLain()
            ->with(['astap.jenisAstap', 'astap.rekeningBelanja', 'user'])
            ->where('tahun', $selectedYear);

        if ($selectedTriwulan !== 'all') {
            $query->where('triwulan', (int) $selectedTriwulan);
        }

        $records = $query->orderBy('tanggal_reklas', 'asc')->get();

        if ($selectedSubKoreksi !== 'all') {
            $records = $records->filter(fn($i) => $i->sub_koreksi === $selectedSubKoreksi);
        }
        if ($selectedTipe !== 'all') {
            $records = $records->filter(fn($i) => $i->tipe_koreksi === $selectedTipe);
        }

        $filename = "Rekap_Audit_Koreksi_Nilai_BMD_TA{$selectedYear}_TW{$selectedTriwulan}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            // Header Kolom CSV Resmi
            fputcsv($handle, [
                'No',
                'Tanggal Transaksi',
                'Tahun',
                'Triwulan',
                'Kategori Koreksi',
                'Sub-Koreksi BMD',
                'NIBAR',
                'Nama Barang / Aset',
                'Kelompok KIB',
                'Kode Akun 108',
                'Rekening Asal',
                'Arah Koreksi',
                'Nilai Koreksi (Rp)',
                'Nilai Buku Semula (Rp)',
                'Nilai Buku Pasca Koreksi (Rp)',
                'Nomor Bukti / Dasar Rekomendasi',
                'Uraian / Alasan Koreksi',
                'Dampak Kolom RMB BPKAD',
                'Petugas Pencatat',
            ], ';');

            $idx = 1;
            foreach ($records as $item) {
                fputcsv($handle, [
                    $idx++,
                    $item->tanggal_reklas?->format('d/m/Y') ?: '-',
                    $item->tahun,
                    'TW ' . $item->triwulan,
                    'Koreksi Nilai BMD',
                    $item->sub_koreksi_label ?: strtoupper($item->sub_koreksi),
                    $item->astap?->nibar ?: '-',
                    $item->astap?->nama_barang ?: '-',
                    $item->astap?->category ?: '-',
                    $item->astap?->kode_108 ?: '-',
                    $item->asal_nama ?: ($item->astap?->rekeningBelanja?->nama_rekening ?: '-'),
                    $item->tipe_koreksi === 'tambah' ? 'Bertambah (+)' : 'Berkurang (-)',
                    number_format((float) $item->nilai_reklas, 2, ',', '.'),
                    number_format((float) $item->nilai_semula, 2, ',', '.'),
                    number_format((float) $item->nilai_setelah_koreksi, 2, ',', '.'),
                    $item->nomor_ba_reklas ?: '-',
                    $item->alasan_reklas ?: ($item->keterangan ?: '-'),
                    $item->dampak_rmb['label'] ?? '-',
                    $item->user?->name ?: 'Administrator',
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }
}

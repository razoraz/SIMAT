<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\PeriodeTutupBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutupBukuController extends Controller
{
    /**
     * Halaman Khusus Manajemen Tutup Buku BMD (Triwulanan & Tahunan)
     */
    public function index(Request $request)
    {
        $currentYear = (int) ($request->input('tahun', date('Y')));
        $actualYear = (int) date('Y');
        $actualMonth = (int) date('n');
        $actualQuarter = (int) ceil($actualMonth / 3);

        // Ambil data semua aset non-deleted
        $astaps = Astap::where('is_deleted', 0)
            ->with([
                'registers' => function($q) { $q->where('is_deleted', 0); },
                'jenisAstap',
                'rekeningBelanja',
                'jenisPengadaan',
                'unit',
                'reklas',
                'kemitraan'
            ])
            ->orderBy('id', 'desc')
            ->get();

        // 1. Matriks Triwulan Tahun Berjalan (TW 1 s/d TW 4)
        $triwulanDefs = [
            1 => ['nama' => 'Triwulan I', 'rentang' => 'Januari - Maret', 'badge' => 'TW I', 'bulan_akhir' => 3],
            2 => ['nama' => 'Triwulan II', 'rentang' => 'April - Juni', 'badge' => 'TW II', 'bulan_akhir' => 6],
            3 => ['nama' => 'Triwulan III', 'rentang' => 'Juli - September', 'badge' => 'TW III', 'bulan_akhir' => 9],
            4 => ['nama' => 'Triwulan IV', 'rentang' => 'Oktober - Desember', 'badge' => 'TW IV', 'bulan_akhir' => 12],
        ];

        // Status DB jika ada catatan kunci manual
        $lockedRecords = PeriodeTutupBuku::where('tahun', $currentYear)->get()->keyBy('triwulan');

        $triwulanData = [];
        foreach ($triwulanDefs as $tw => $def) {
            // Aturan Otomatis:
            // Jika tahun dipilih < tahun sekarang -> otomatis tutup buku
            // Jika tahun dipilih == tahun sekarang dan triwulan < triwulan sekarang -> otomatis tutup buku
            $isAutoPassed = ($currentYear < $actualYear) || ($currentYear == $actualYear && $tw < $actualQuarter);
            $isActive = ($currentYear == $actualYear && $tw == $actualQuarter);
            $isFuture = ($currentYear > $actualYear) || ($currentYear == $actualYear && $tw > $actualQuarter);

            $manualRecord = $lockedRecords->get($tw);
            $isLocked = $isAutoPassed || (bool) ($manualRecord?->is_locked ?? false);

            // Filter aset untuk triwulan ini
            $itemsTw = $astaps->filter(function($item) use ($currentYear, $tw) {
                $itemYear = (int) ($item->tahun_perolehan ?: ($item->kemitraan?->tahun ?? 0));
                if ($itemYear !== $currentYear) return false;
                $itemTw = PeriodeTutupBuku::parseTriwulan($item->triwulan);
                return $itemTw === $tw;
            });

            $totalItem = $itemsTw->count();
            $totalVolume = $itemsTw->sum(fn($i) => (int) ($i->jumlah_volume ?: 1));
            $totalNominal = $itemsTw->sum(fn($i) => (float) ($i->total_realisasi ?: 0));

            $triwulanData[] = [
                'triwulan'                => $tw,
                'nama'                    => $def['nama'],
                'rentang'                 => $def['rentang'],
                'badge'                   => $def['badge'],
                'is_locked'               => $isLocked,
                'is_auto_passed'          => $isAutoPassed,
                'is_active'               => $isActive,
                'is_future'               => $isFuture,
                'status_label'            => $isLocked ? 'Ditutup Buku' : ($isActive ? 'Sedang Berjalan' : 'Belum Berjalan'),
                'total_item'              => $totalItem,
                'total_volume'            => $totalVolume,
                'total_nominal'           => $totalNominal,
                'total_nominal_formatted' => 'Rp ' . number_format($totalNominal, 0, ',', '.'),
                'nomor_bar'               => $manualRecord?->nomor_bar_bpkad ?: ($isLocked ? 'Otomatis Sistem (Triwulan Berlalu)' : '-'),
                'tanggal_tutup'           => $manualRecord?->tanggal_tutup?->format('d/m/Y') ?: ($isLocked ? 'Auto-Closed' : '-'),
            ];
        }

        // 2. Tutup Buku Tahunan (Arsip Tahun-Tahun Sebelumnya)
        $distinctYears = $astaps->pluck('tahun_perolehan')
            ->filter()
            ->map(fn($y) => (int)$y)
            ->unique()
            ->filter(fn($y) => $y > 2000)
            ->sortDesc()
            ->values();

        if (!$distinctYears->contains($actualYear)) {
            $distinctYears->prepend($actualYear);
            $distinctYears = $distinctYears->sortDesc()->values();
        }

        $tahunanData = [];
        foreach ($distinctYears as $yr) {
            $isPastYear = $yr < $actualYear;
            $itemsYr = $astaps->filter(fn($i) => (int)$i->tahun_perolehan === $yr);

            $totalItem = $itemsYr->count();
            $totalVolume = $itemsYr->sum(fn($i) => (int) ($i->jumlah_volume ?: 1));
            $totalNominal = $itemsYr->sum(fn($i) => (float) ($i->total_realisasi ?: 0));

            $annualRecord = PeriodeTutupBuku::where('tahun', $yr)->where('triwulan', 0)->first();
            $isLocked = $isPastYear || (bool) ($annualRecord?->is_locked ?? false);

            $tahunanData[] = [
                'tahun'                   => $yr,
                'nama'                    => "Tahun Anggaran {$yr}",
                'keterangan'              => $isPastYear ? 'Tutup Buku Tahunan Final (Audited LKPD BPK RI)' : 'Tahun Berjalan (Belum Year-End Closing)',
                'is_locked'               => $isLocked,
                'is_past_year'            => $isPastYear,
                'status_label'            => $isLocked ? 'Ditutup Buku Tahunan' : 'Tahun Berjalan',
                'total_item'              => $totalItem,
                'total_volume'            => $totalVolume,
                'total_nominal'           => $totalNominal,
                'total_nominal_formatted' => 'Rp ' . number_format($totalNominal, 0, ',', '.'),
                'nomor_bar'               => $annualRecord?->nomor_bar_bpkad ?: ($isPastYear ? 'LKPD BPK RI / BPKAD' : '-'),
            ];
        }

        // Mapped Astap List untuk Javascript Alpine.js
        $astapsJson = $astaps->map(function($a) {
            $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
            $ja = $a->jenisAstap;
            $kode108Val = $a->kode_108 ?: ($ja ? ($ja->sub_sub_rincian_objek ?: $ja->jenis) : '');
            $latestReklas = $a->reklas ? $a->reklas->first() : null;

            return [
                'id'                  => $a->id,
                'category'            => $a->category,
                'kode_barang'         => $kode108Val,
                'nama_barang'         => $a->nama_barang,
                'tahun_perolehan'     => (int) ($a->tahun_perolehan ?: date('Y')),
                'triwulan'            => $a->triwulan ?: ($spec['triwulan'] ?? 'TW I'),
                'triwulan_num'        => PeriodeTutupBuku::parseTriwulan($a->triwulan),
                'volume_satuan'       => ($a->jumlah_volume ?: 1) . ' ' . ($a->satuan ?: 'Aset'),
                'jumlah_volume'       => (int) ($a->jumlah_volume ?: 1),
                'satuan'              => $a->satuan ?: 'Unit',
                'harga_satuan'        => (float) $a->harga_satuan,
                'jumlah_anggaran'     => (float) ($a->jumlah_anggaran ?: 0),
                'jumlah_realisasi'    => 'Rp ' . number_format($a->total_realisasi, 0, ',', '.'),
                'total_realisasi_num' => (float) $a->total_realisasi,
                'kondisi'             => $a->kondisi ?: 'Baik',
                'sumber_dana'         => $a->isKemitraan() ? 'kemitraan' : ($a->sumber_dana ?: 'belanja_modal'),
                'sumber_dana_raw'     => $a->sumber_dana,
                'jenis_aset_nama'     => $ja ? ($ja->uraian_sub_rincian ?: $ja->nama_jenis) : 'Aset Tetap',
                'is_reklas'           => (bool) $a->is_reklas,
                'jenis_reklas'        => $a->jenis_reklas,
                'unit_id'             => $a->unit_id,
                'unit_nama'           => $a->unit?->nama ?? '',
                'rekening_belanja'    => $a->rekeningBelanja ? $a->rekeningBelanja->nama_rekening : '',
                'spk_nomor'           => $a->nomor_spk,
                'spk_tanggal'         => $a->tanggal_spk ? $a->tanggal_spk->format('Y-m-d') : '',
                'bap_nomor'           => $a->nomor_bap,
                'bap_tanggal'         => $a->tanggal_bap ? $a->tanggal_bap->format('Y-m-d') : '',
                'bast_dokumen_nomor'  => $a->nomor_bast,
                'bast_dokumen_tanggal'=> $a->tanggal_bast ? $a->tanggal_bast->format('Y-m-d') : '',
                'keterangan'          => $a->keterangan,
                'registers'           => $a->registers ? $a->registers->map(fn($r) => [
                    'id'             => $r->id,
                    'no_register'    => $r->nibar ?: $r->no_register,
                    'nibar'          => $r->nibar,
                    'ruang_pemegang' => $r->ruang_pemegang ?: ($r->unit?->nama ?? '-'),
                    'kondisi'        => $r->kondisi ?: 'Baik',
                ])->values() : [],
                'spesifikasi_json'    => $spec,
            ];
        });

        $dbMaster108 = \App\Models\JenisAstap::getNested108();
        $dbMitraKemitraans = \App\Models\AstapKemitraan::getDistinctMitras();

        $astaps = $astapsJson;

        return view('pages.tutup_buku.index', compact(
            'currentYear',
            'actualYear',
            'actualQuarter',
            'triwulanData',
            'tahunanData',
            'astaps',
            'astapsJson',
            'distinctYears',
            'dbMaster108',
            'dbMitraKemitraans'
        ));
    }

    /**
     * Dapatkan status penutupan buku untuk semua triwulan pada tahun tertentu
     */
    public function status(Request $request)
    {
        $tahun = (int) ($request->input('tahun', date('Y')));

        $records = PeriodeTutupBuku::with(['lockedByUser', 'unlockedByUser'])
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('triwulan');

        $isAnnualLocked = (bool) ($records->get(0)?->is_locked ?? false);

        // Ambil data aset ASTAP untuk tahun terpilih untuk menghitung nilai dan volume terkunci
        $astapsForYear = \App\Models\Astap::where('is_deleted', 0)
            ->where('tahun_perolehan', $tahun)
            ->get();

        $twGroups = [];
        foreach ($astapsForYear as $item) {
            $itemTw = PeriodeTutupBuku::parseTriwulan($item->triwulan);
            if (!isset($twGroups[$itemTw])) {
                $twGroups[$itemTw] = ['count' => 0, 'volume' => 0, 'nominal' => 0];
            }
            $twGroups[$itemTw]['count']++;
            $twGroups[$itemTw]['volume'] += (int) ($item->jumlah_volume ?: 1);
            $twGroups[$itemTw]['nominal'] += (float) ($item->total_realisasi ?: 0);
        }

        $periods = [];
        $definitions = [
            1 => ['nama' => 'Triwulan 1', 'rentang' => 'Januari - Maret', 'badge' => 'TW 1'],
            2 => ['nama' => 'Triwulan 2', 'rentang' => 'April - Juni', 'badge' => 'TW 2'],
            3 => ['nama' => 'Triwulan 3', 'rentang' => 'Juli - September', 'badge' => 'TW 3'],
            4 => ['nama' => 'Triwulan 4', 'rentang' => 'Oktober - Desember', 'badge' => 'TW 4'],
            0 => ['nama' => 'Tutup Buku Tahunan', 'rentang' => 'Per 31 Desember (Audit LKPD BPK RI)', 'badge' => 'Tahunan'],
        ];

        foreach ($definitions as $tw => $def) {
            $rec = $records->get($tw);
            $lockedDirectly = (bool) ($rec?->is_locked ?? false);
            // Jika triwulan 1-4, tapi kunci tahunan (0) aktif, maka otomatis terkunci
            $effectiveLocked = ($tw !== 0 && $isAnnualLocked) ? true : $lockedDirectly;

            if ($tw === 0) {
                $statCount = $astapsForYear->count();
                $statVol = $astapsForYear->sum(fn($i) => (int)($i->jumlah_volume ?: 1));
                $statNominal = $astapsForYear->sum(fn($i) => (float)($i->total_realisasi ?: 0));
            } else {
                $statCount = $twGroups[$tw]['count'] ?? 0;
                $statVol = $twGroups[$tw]['volume'] ?? 0;
                $statNominal = $twGroups[$tw]['nominal'] ?? 0;
            }

            $periods[] = [
                'triwulan'                => $tw,
                'nama'                    => $def['nama'],
                'rentang'                 => $def['rentang'],
                'badge'                   => $def['badge'],
                'is_locked'               => $effectiveLocked,
                'is_annual_parent'        => ($tw !== 0 && $isAnnualLocked && !$lockedDirectly),
                'nomor_bar_bpkad'         => $rec?->nomor_bar_bpkad ?: ($isAnnualLocked ? ($records->get(0)?->nomor_bar_bpkad ?: '-') : '-'),
                'tanggal_tutup'           => $rec?->tanggal_tutup?->format('d/m/Y') ?: ($isAnnualLocked ? ($records->get(0)?->tanggal_tutup?->format('d/m/Y') ?: '-') : '-'),
                'keterangan'              => $rec?->keterangan ?: '-',
                'locked_by_name'          => $rec?->lockedByUser?->name ?: ($isAnnualLocked ? ($records->get(0)?->lockedByUser?->name ?: '-') : '-'),
                'locked_at'               => $rec?->locked_at?->format('d/m/Y H:i') ?: '-',
                'unlocked_by_name'        => $rec?->unlockedByUser?->name ?: null,
                'unlocked_at'             => $rec?->unlocked_at?->format('d/m/Y H:i') ?: null,
                'alasan_unlock'           => $rec?->alasan_unlock ?: null,
                'total_item'              => $statCount,
                'total_volume'            => $statVol,
                'total_nominal'           => $statNominal,
                'total_nominal_formatted' => 'Rp ' . number_format($statNominal, 0, ',', '.'),
            ];
        }

        return response()->json([
            'success'          => true,
            'tahun'            => $tahun,
            'is_annual_locked' => $isAnnualLocked,
            'periods'          => $periods,
        ]);
    }

    /**
     * Eksekusi Penguncian Periode (Tutup Buku Triwulanan / Tahunan)
     */
    public function lock(Request $request)
    {
        $validated = $request->validate([
            'tahun'           => 'required|integer|min:2000|max:2100',
            'triwulan'        => 'required|integer|in:0,1,2,3,4',
            'nomor_bar_bpkad' => 'required|string|max:150',
            'tanggal_tutup'   => 'required|date',
            'keterangan'      => 'nullable|string|max:1000',
        ], [
            'nomor_bar_bpkad.required' => 'Nomor Berita Acara Rekonsiliasi (BAR) BPKAD wajib diisi sebagai dasar legalitas penutupan buku.',
            'tanggal_tutup.required'   => 'Tanggal penutupan buku wajib diisi.',
        ]);

        $user = Auth::user();

        $record = PeriodeTutupBuku::updateOrCreate(
            [
                'tahun'    => $validated['tahun'],
                'triwulan' => $validated['triwulan'],
            ],
            [
                'is_locked'       => true,
                'nomor_bar_bpkad' => $validated['nomor_bar_bpkad'],
                'tanggal_tutup'   => $validated['tanggal_tutup'],
                'keterangan'      => $validated['keterangan'] ?? null,
                'locked_by'       => $user->id,
                'locked_at'       => now(),
            ]
        );

        $label = $validated['triwulan'] == 0 
            ? "Tutup Buku Tahunan T.A. {$validated['tahun']}" 
            : "Triwulan {$validated['triwulan']} T.A. {$validated['tahun']}";

        return response()->json([
            'success' => true,
            'message' => "Periode {$label} berhasil ditutup buku resmi. Data aset kini berstatus Read-Only dan dilindungi dari perubahan.",
            'data'    => $record,
        ]);
    }

    /**
     * Eksekusi Pembukaan Kunci Periode (Emergency Revision / Buka Buku)
     * Hanya diizinkan untuk peran Master Admin dengan alasan tertulis
     */
    public function unlock(Request $request)
    {
        $validated = $request->validate([
            'tahun'         => 'required|integer|min:2000|max:2100',
            'triwulan'      => 'required|integer|in:0,1,2,3,4',
            'alasan_unlock' => 'required|string|min:5|max:1000',
        ], [
            'alasan_unlock.required' => 'Alasan pembukaan kunci wajib diisi demi transparansi dan kepatuhan audit trail BPK.',
            'alasan_unlock.min'      => 'Uraikan alasan pembukaan kunci dengan jelas (minimal 5 karakter).',
        ]);

        $user = Auth::user();
        if ($user->role !== 'master_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pembukaan kunci periode tutup buku hanya dapat dilakukan oleh Master Administrator.',
            ], 403);
        }

        $record = PeriodeTutupBuku::where('tahun', $validated['tahun'])
            ->where('triwulan', $validated['triwulan'])
            ->first();

        if (!$record || !$record->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tersebut saat ini tidak dalam status terkunci.',
            ], 422);
        }

        $record->update([
            'is_locked'     => false,
            'unlocked_by'   => $user->id,
            'unlocked_at'   => now(),
            'alasan_unlock' => $validated['alasan_unlock'],
        ]);

        $label = $validated['triwulan'] == 0 
            ? "Tutup Buku Tahunan T.A. {$validated['tahun']}" 
            : "Triwulan {$validated['triwulan']} T.A. {$validated['tahun']}";

        return response()->json([
            'success' => true,
            'message' => "Kunci periode {$label} berhasil dibuka kembali. Alasan revisi telah tercatat di jejak audit.",
            'data'    => $record,
        ]);
    }
}

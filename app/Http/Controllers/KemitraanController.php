<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapKemitraan;
use App\Models\JenisAstap;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KemitraanController extends Controller
{
    /**
     * Tampilkan Halaman Utama Master Data Kemitraan Aset (KSO, BGS, Sewa Akun 1.5.2)
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        // Sinkronisasi otomatis: Jika ada Astap dengan sumber_dana kemitraan yang belum masuk astap_kemitraans
        $unlinkedKemitraans = Astap::where('sumber_dana', 'kemitraan')
            ->where('is_deleted', 0)
            ->whereDoesntHave('kemitraan')
            ->get();

        // BUG-04 FIX: Bungkus auto-sync dalam DB::transaction agar tidak setengah tersinkronisasi
        if ($unlinkedKemitraans->isNotEmpty()) {
            DB::transaction(function () use ($unlinkedKemitraans) {
                foreach ($unlinkedKemitraans as $astap) {
                    $spec = $astap->spesifikasi_json ?? [];
                    AstapKemitraan::create([
                        'astap_id'         => $astap->id,
                        'mitra_nama'       => $spec['mitra_nama'] ?? 'Mitra Pihak Ketiga',
                        'nomor_pks'        => $astap->bast_dokumen_nomor ?? ($spec['nomor_pks'] ?? '-'),
                        'tanggal_pks'      => $astap->bast_dokumen_tanggal ?? ($spec['tanggal_pks'] ?? now()),
                        'skema_kemitraan'  => $spec['skema_kemitraan'] ?? 'KSO',
                        'tanggal_mulai'    => $spec['tanggal_mulai'] ?? null,
                        'tanggal_selesai'  => $spec['tanggal_selesai'] ?? null,
                        'status_konsesi'   => 'Aktif',
                        'jumlah_volume'    => max(1, (int) $astap->jumlah_volume),
                        'satuan'           => $astap->satuan ?: 'Unit',
                        'nilai_aset'       => (float) $astap->total_realisasi,
                        'tahun'            => (int) ($astap->tahun_perolehan ?: date('Y')),
                        'triwulan'         => $astap->triwulan ?: 'TW I',
                        'keterangan'       => $astap->keterangan_tambahan ?? ($spec['keterangan'] ?? null),
                        'user_id'          => $astap->user_id ?? Auth::id(),
                    ]);
                }
            });
        }

        $filterSkema  = $request->query('skema', 'all');   // 'all', 'KSO', 'BGS', 'BSG', 'KSP', 'Sewa'
        $filterTahun  = $request->query('tahun', 'all');
        $filterTw     = $request->query('triwulan', 'all');
        $filterStatus = $request->query('status', 'all');  // 'all', 'Aktif', 'Akan Berakhir', 'Selesai / Reklasifikasi'
        $search       = trim($request->query('search', ''));

        // Query utama data kemitraan (hanya yang aktif / belum dihapus)
        $query = AstapKemitraan::with(['astap.jenisAstap', 'astap.registers.unit', 'astap.unit', 'user'])
            ->where('is_deleted', 0)
            ->whereHas('astap', function ($q) {
                $q->where('is_deleted', 0);
            })
            ->orderBy('tanggal_pks', 'desc')
            ->orderBy('id', 'desc');

        if ($filterSkema !== 'all') {
            // BUG-05 FIX: support nilai 'BGS/BSG' dari form (yang difilter sebagai BGS atau BSG)
            if ($filterSkema === 'BGS' || $filterSkema === 'BSG') {
                $query->where(function ($q) use ($filterSkema) {
                    $q->where('skema_kemitraan', $filterSkema)
                      ->orWhere('skema_kemitraan', 'BGS/BSG');
                });
            } else {
                $query->where('skema_kemitraan', $filterSkema);
            }
        }

        if ($filterTahun !== 'all') {
            $query->where('tahun', $filterTahun);
        }

        if ($filterTw !== 'all') {
            $query->where('triwulan', $filterTw);
        }

        if ($filterStatus !== 'all') {
            $query->where('status_konsesi', $filterStatus);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('mitra_nama', 'like', "%{$search}%")
                  ->orWhere('nomor_pks', 'like', "%{$search}%")
                  ->orWhere('skema_kemitraan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('astap', function ($sq) use ($search) {
                      $sq->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_108', 'like', "%{$search}%");
                  });
            });
        }

        $kemitraanRecords = $query->get();

        // BUG-14 FIX: Hitung KPI via query aggregate — hindari memuat seluruh collection ke RAM
        $kpiBase = AstapKemitraan::where('is_deleted', 0)
            ->whereHas('astap', fn($q) => $q->where('is_deleted', 0));

        $totalNilaiKemitraan = (clone $kpiBase)->sum('nilai_aset');
        $totalVolumeUnit     = (clone $kpiBase)->sum('jumlah_volume');
        $totalAktif          = (clone $kpiBase)->where('status_konsesi', 'Aktif')->count();
        $totalMitraUnik      = (clone $kpiBase)->distinct('mitra_nama')->count('mitra_nama');

        // Daftar Unit & Jenis 108 untuk modal atau filter
        $dbUnits = Unit::orderBy('nama')->get();
        $dbMaster108 = JenisAstap::getNested108();

        return view('pages.kemitraan.index', compact(
            'kemitraanRecords',
            'totalNilaiKemitraan',
            'totalVolumeUnit',
            'totalAktif',
            'totalMitraUnik',
            'dbUnits',
            'dbMaster108',
            'filterSkema',
            'filterTahun',
            'filterTw',
            'filterStatus',
            'search'
        ));
    }

    /**
     * Update Status Konsesi Kerjasama (misal: Selesai untuk siap direklasifikasi)
     */
    public function updateStatus(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::findOrFail($id);

        $request->validate([
            'status_konsesi' => 'required|string|in:Aktif,Akan Berakhir,Selesai / Reklasifikasi,Dihentikan',
            'keterangan'     => 'nullable|string|max:1000'
        ]);

        $kemitraan->status_konsesi = $request->input('status_konsesi');
        if ($request->filled('keterangan')) {
            $kemitraan->keterangan = ($kemitraan->keterangan ? $kemitraan->keterangan . "\n" : '') .
                '[' . date('d/m/Y') . '] Status: ' . $request->input('status_konsesi') . ' - ' . $request->input('keterangan');
        }
        $kemitraan->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status kerja sama {$kemitraan->nomor_pks} berhasil diperbarui menjadi {$kemitraan->status_konsesi}."
            ]);
        }

        return redirect()->route('master.kemitraan')
            ->with('success', "Status kerja sama {$kemitraan->nomor_pks} berhasil diperbarui.");
    }

    /**
     * Hapus / Pindahkan Catatan Aset Kemitraan ke Pusat Pemulihan Data (Soft Delete)
     */
    public function destroy(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::findOrFail($id);
        $alasanHapus = $request->input('alasan_hapus', 'Dihapus dari Kelola Kemitraan Aset');

        DB::transaction(function () use ($kemitraan, $alasanHapus) {
            $user = Auth::user();
            $deleterName = $user ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') : 'Administrator';
            $deleterId = $user?->id;
            $now = now();

            // 1. Soft delete catatan kemitraan
            $kemitraan->softDelete($alasanHapus);

            // 2. Soft delete aset ASTAP dan unit registernya
            $astap = $kemitraan->astap;
            if ($astap) {
                AstapRegister::where('astap_id', $astap->id)->update([
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $deleterId,
                    'deleted_at'    => $now,
                ]);

                $astap->update([
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $deleterId,
                    'deleted_at'    => $now,
                ]);
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Aset Kemitraan berhasil dipindahkan ke Pusat Pemulihan Data (Recycle Bin).'
            ]);
        }

        return redirect()->route('master.kemitraan')
            ->with('success', 'Data Aset Kemitraan berhasil dipindahkan ke Pusat Pemulihan Data (Recycle Bin).');
    }
}

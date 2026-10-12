<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapKemitraan;
use App\Models\AstapReklas;
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

        // Sinkronisasi status konsesi: Jika tanggal_selesai sudah lewat, ubah status 'Aktif' menjadi 'Konsesi Berakhir' (Bulk update efisien)
        $today = Carbon::today();
        AstapKemitraan::where('is_deleted', 0)
            ->where('status_konsesi', 'Aktif')
            ->whereNotNull('tanggal_selesai')
            ->where('tanggal_selesai', '<', $today)
            ->update(['status_konsesi' => 'Konsesi Berakhir']);

        $filterKib    = $request->query('kib', 'all');
        $filterSkema  = $request->query('skema', 'all');   // 'all', 'KSO', 'BGS', 'BSG', 'KSP', 'Sewa'
        $filterTahun  = $request->query('tahun', 'all');
        $filterTw     = $request->query('triwulan', 'all');
        $filterStatus = $request->query('status', 'all');  // 'all', 'Aktif', 'Akan Berakhir', 'Konsesi Berakhir', 'Selesai / Reklasifikasi'
        $search       = trim($request->query('search', ''));

        // Query utama data kemitraan (hanya yang aktif / belum dihapus)
        // Pengambilan dataset lengkap untuk pemfilteran instan reaktif di client-side (tanpa page reload seperti Data ASTAP)
        $query = AstapKemitraan::with([
            'astap.jenisAstap', 
            'astap.registers.unit', 
            'astap.unit', 
            'astap.reklas',
            'user', 
            'objekRegister.astap.reklas', 
            'objekAstap.reklas'
        ])
            ->where('is_deleted', 0)
            ->whereHas('astap', function ($q) {
                $q->where('is_deleted', 0);
            })
            ->orderBy('tanggal_pks', 'desc')
            ->orderBy('id', 'desc');

        $kemitraanRecords = $query->get();

        // BUG-14 FIX: Hitung KPI via query aggregate — hindari memuat seluruh collection ke RAM
        $kpiBase = AstapKemitraan::where('is_deleted', 0)
            ->whereHas('astap', fn($q) => $q->where('is_deleted', 0));

        $totalNilaiKemitraan = (clone $kpiBase)->sum('nilai_aset');
        $totalVolumeUnit     = (clone $kpiBase)->sum('jumlah_volume');
        $totalAktif          = (clone $kpiBase)
            ->where('status_konsesi', 'Aktif')
            ->where(function ($q) use ($today) {
                $q->whereNull('tanggal_selesai')->orWhere('tanggal_selesai', '>=', $today);
            })->count();
        $totalBerakhir       = (clone $kpiBase)
            ->where(function ($q) use ($today) {
                $q->where('status_konsesi', 'Konsesi Berakhir')
                  ->orWhere(function ($sq) use ($today) {
                      $sq->where('status_konsesi', 'Aktif')
                         ->whereNotNull('tanggal_selesai')
                         ->where('tanggal_selesai', '<', $today);
                  });
            })->count();
        $totalMitraUnik      = (clone $kpiBase)->distinct('mitra_nama')->count('mitra_nama');

        // Daftar Unit & Jenis 108 untuk modal atau filter
        $dbUnits = Unit::orderBy('nama')->get();
        $dbMaster108 = JenisAstap::getNested108();

        // Ambil data Astap kemitraan lengkap untuk kebutuhan Engine Ekspor Excel Multi-Sheet (Client-Side)
        $kemitraanAstaps = Astap::with(['registers.unit', 'kemitraan', 'jenisAstap', 'unit'])
            ->where('is_deleted', 0)
            ->where(function ($q) {
                $q->where('sumber_dana', 'kemitraan')
                  ->orWhereHas('kemitraan', fn($sq) => $sq->where('is_deleted', 0));
            })
            ->get();

        $dbMitraKemitraans = AstapKemitraan::getDistinctMitras();

        // Ambil ID Astap yang SUDAH terdaftar di master kemitraan (baik aktif maupun yang sudah dihapus, agar tidak lolos dari filter reklas)
        $allKemitraanAstapIds = AstapKemitraan::pluck('astap_id')
            ->merge(AstapKemitraan::whereNotNull('objek_astap_id')->pluck('objek_astap_id'))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Ambil riwayat reklasifikasi aset BMD RSUD ke Kemitraan (1.5.2) HANYA untuk aset yang BELUM dibuatkan PKS Kemitraan
        // DAN HANYA UNTUK ASET & REKLAS YANG BELUM DIHAPUS (is_deleted = 0)
        $reklasQuery = AstapReklas::where('is_deleted', 0)
            ->where(function ($rq) {
                $rq->where('tujuan_kib', 'KEMITRAAN')
                   ->orWhere('tujuan_kode', 'like', '1.5.2%');
            })
            ->whereNotIn('astap_id', $allKemitraanAstapIds)
            ->whereHas('astap', function ($q) {
                $q->where('is_deleted', 0);
            })
            ->with(['astap.registers.unit', 'astap.jenisAstap', 'astap.unit'])
            ->orderBy('tanggal_reklas', 'desc')
            ->orderBy('id', 'desc');

        $reklasKemitraanRecords = $reklasQuery->get()->unique('astap_id');

        return view('pages.kemitraan.index', compact(
            'kemitraanRecords',
            'kemitraanAstaps',
            'reklasKemitraanRecords',
            'totalNilaiKemitraan',
            'totalVolumeUnit',
            'totalAktif',
            'totalBerakhir',
            'totalMitraUnik',
            'dbUnits',
            'dbMaster108',
            'filterKib',
            'filterSkema',
            'filterTahun',
            'filterTw',
            'filterStatus',
            'search',
            'dbMitraKemitraans'
        ));
    }

    /**
     * Update Status Konsesi Kerjasama (misal: Selesai untuk siap direklasifikasi)
     */
    public function updateStatus(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::findOrFail($id);

        $request->validate([
            'status_konsesi' => 'required|string|in:Aktif,Konsesi Berakhir,Selesai,Selesai / Reklasifikasi,Dihentikan,Dikembalikan ke Rekanan'
        ]);

        $newStatus = $request->input('status_konsesi');
        $kemitraan->status_konsesi = $newStatus;
        $kemitraan->save();

        // Sinkronisasi status register fisik aset
        if ($kemitraan->astap_id) {
            if ($newStatus === 'Dikembalikan ke Rekanan') {
                AstapRegister::where('astap_id', $kemitraan->astap_id)
                    ->update([
                        'status'         => 'Dikembalikan ke Rekanan',
                        'ruang_pemegang' => 'Telah Dikembalikan ke Rekanan',
                    ]);
            } elseif ($newStatus === 'Aktif') {
                AstapRegister::where('astap_id', $kemitraan->astap_id)
                    ->where('status', 'Dikembalikan ke Rekanan')
                    ->update([
                        'status' => 'Tersedia',
                    ]);
            }
        }

        // Sinkronisasi status dari Objek Pemanfaatan BMD RSUD (Induk) ke aset mitra terkait
        if ($kemitraan->tipe_kemitraan === 'dimanfaatkan') {
            AstapKemitraan::where('objek_astap_id', $kemitraan->astap_id)
                ->where('is_deleted', 0)
                ->where('nomor_pks', $kemitraan->nomor_pks)
                ->update(['status_konsesi' => $newStatus]);
        }

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
     * Perpanjang Masa Konsesi / Adendum PKS Kemitraan (Extend)
     */
    public function extendKonsesi(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::find($id);
        if (!$kemitraan) {
            $kemitraan = AstapKemitraan::where('astap_id', $id)->firstOrFail();
        }

        $request->validate([
            'nomor_adendum'        => 'required|string|max:150',
            'tanggal_adendum'      => 'required|date',
            'tanggal_selesai_baru' => 'required|date',
            'nilai_aset_baru'      => 'nullable',
            'keterangan_adendum'   => 'nullable|string|max:1000',
            'berkas_adendum'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        return DB::transaction(function () use ($request, $kemitraan) {
            $user = Auth::user();
            $userName = $user ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') : 'Administrator';

            // Upload berkas jika ada
            $uploadedPath = $kemitraan->dokumen_adendum_path;
            if ($request->hasFile('berkas_adendum')) {
                $file = $request->file('berkas_adendum');
                $filename = 'adendum_' . $kemitraan->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $uploadedPath = $file->storeAs('kemitraan/adendum', $filename, 'public');
            }

            // Parsing nilai baru jika ada
            $nilaiBaru = $kemitraan->nilai_aset;
            if ($request->filled('nilai_aset_baru')) {
                $rawNilai = str_replace(['Rp', '.', ' '], '', $request->input('nilai_aset_baru'));
                $rawNilai = str_replace(',', '.', $rawNilai);
                if (is_numeric($rawNilai)) {
                    $nilaiBaru = (float) $rawNilai;
                }
            }

            $currentHistory = $kemitraan->riwayat_adendum ?: [];
            if (!is_array($currentHistory)) {
                $currentHistory = json_decode($currentHistory, true) ?: [];
            }

            $nextExtendNo = ($kemitraan->jumlah_extend ?? 0) + 1;
            $tglSelesaiLama = $kemitraan->tanggal_selesai ? Carbon::parse($kemitraan->tanggal_selesai)->format('Y-m-d') : null;

            $newAdendumEntry = [
                'extend_ke'            => $nextExtendNo,
                'nomor_adendum'        => $request->input('nomor_adendum'),
                'tanggal_adendum'      => $request->input('tanggal_adendum'),
                'tanggal_selesai_lama' => $tglSelesaiLama,
                'tanggal_selesai_baru' => $request->input('tanggal_selesai_baru'),
                'nilai_aset_lama'      => (float) $kemitraan->nilai_aset,
                'nilai_aset_baru'      => (float) $nilaiBaru,
                'keterangan'           => $request->input('keterangan_adendum') ?: '-',
                'berkas_path'          => $uploadedPath,
                'created_by'           => $userName,
                'created_at'           => now()->toDateTimeString(),
            ];

            $currentHistory[] = $newAdendumEntry;

            // Simpan perubahan ke record kemitraan
            $kemitraan->tanggal_selesai      = $request->input('tanggal_selesai_baru');
            $kemitraan->status_konsesi       = 'Aktif';
            $kemitraan->is_extended          = 1;
            $kemitraan->jumlah_extend        = $nextExtendNo;
            $kemitraan->nilai_aset           = $nilaiBaru;
            $kemitraan->riwayat_adendum      = $currentHistory;
            $kemitraan->dokumen_adendum_path = $uploadedPath;
            $kemitraan->save();

            // Cek apakah ada aset mitra (Tabel 2) yang terhubung pada objek pemanfaatan ini
            $targetAstapId = $kemitraan->astap_id ?: $kemitraan->id;
            $linkedMitraList = collect([]);
            if ($kemitraan->tipe_kemitraan === 'dimanfaatkan' || empty($kemitraan->objek_astap_id)) {
                $linkedMitraList = AstapKemitraan::where('objek_astap_id', $targetAstapId)
                    ->where('is_deleted', 0)
                    ->with(['astap.registers'])
                    ->get();
            }

            $formattedLinkedMitra = $linkedMitraList->map(function ($m) {
                return [
                    'id'                   => $m->id,
                    'astap_id'             => $m->astap_id,
                    'nama_barang'          => $m->astap?->nama_barang ?: 'Aset Mitra',
                    'kode_108'             => $m->astap?->kode_108 ?: ($m->astap?->kode_barang ?: '-'),
                    'nibar'                => $m->objek_nibar ?: ($m->astap?->registers?->first()?->nibar ?: '-'),
                    'tanggal_selesai_lama' => $m->tanggal_selesai ? Carbon::parse($m->tanggal_selesai)->format('d/m/Y') : '-',
                    'status_konsesi'       => $m->status_konsesi ?: 'Aktif',
                ];
            });

            $tglBaruFormatted = Carbon::parse($request->input('tanggal_selesai_baru'))->format('d/m/Y');

            return response()->json([
                'success'              => true,
                'message'              => "Masa pemanfaatan PKS {$kemitraan->nomor_pks} berhasil diperpanjang (Adendum #{$nextExtendNo}) sampai dengan {$tglBaruFormatted}.",
                'kemitraan_id'         => $kemitraan->id,
                'tanggal_selesai_baru' => $request->input('tanggal_selesai_baru'),
                'tanggal_selesai_fmt'  => $tglBaruFormatted,
                'nomor_adendum'        => $request->input('nomor_adendum'),
                'tanggal_adendum'      => $request->input('tanggal_adendum'),
                'has_linked_mitra'     => $linkedMitraList->isNotEmpty(),
                'linked_mitra_count'   => $linkedMitraList->count(),
                'linked_mitra_assets'  => $formattedLinkedMitra,
                'riwayat_adendum'      => $currentHistory,
            ]);
        });
    }

    /**
     * Batch Extend untuk Aset Mitra (Tabel 2) yang terhubung ke Objek Pemanfaatan
     */
    public function extendMitraBatch(Request $request)
    {
        $request->validate([
            'mitra_ids'            => 'required|array|min:1',
            'mitra_ids.*'          => 'required|integer',
            'tanggal_selesai_baru' => 'required|date',
            'nomor_adendum'        => 'required|string|max:150',
            'tanggal_adendum'      => 'nullable|date',
            'keterangan'           => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($request) {
            $user = Auth::user();
            $userName = $user ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') : 'Administrator';
            $now = now();
            $mitraIds = $request->input('mitra_ids');
            $tglBaru = $request->input('tanggal_selesai_baru');
            $noAdendum = $request->input('nomor_adendum');
            $tglAdendum = $request->input('tanggal_adendum') ?: $now->format('Y-m-d');
            $keterangan = $request->input('keterangan') ?: "Ikut perpanjangan masa pemanfaatan PKS Adendum No. {$noAdendum}";

            $updatedCount = 0;
            $updatedRecords = [];

            $mitras = AstapKemitraan::whereIn('id', $mitraIds)->where('is_deleted', 0)->get();

            foreach ($mitras as $mitra) {
                $currentHistory = $mitra->riwayat_adendum ?: [];
                if (!is_array($currentHistory)) {
                    $currentHistory = json_decode($currentHistory, true) ?: [];
                }

                $nextExtendNo = ($mitra->jumlah_extend ?? 0) + 1;
                $tglLama = $mitra->tanggal_selesai ? Carbon::parse($mitra->tanggal_selesai)->format('Y-m-d') : null;

                $currentHistory[] = [
                    'extend_ke'            => $nextExtendNo,
                    'nomor_adendum'        => $noAdendum,
                    'tanggal_adendum'      => $tglAdendum,
                    'tanggal_selesai_lama' => $tglLama,
                    'tanggal_selesai_baru' => $tglBaru,
                    'nilai_aset_lama'      => (float) $mitra->nilai_aset,
                    'nilai_aset_baru'      => (float) $mitra->nilai_aset,
                    'keterangan'           => $keterangan,
                    'berkas_path'          => null,
                    'created_by'           => $userName,
                    'created_at'           => $now->toDateTimeString(),
                ];

                $mitra->tanggal_selesai = $tglBaru;
                $mitra->status_konsesi  = 'Aktif';
                $mitra->is_extended     = 1;
                $mitra->jumlah_extend   = $nextExtendNo;
                $mitra->riwayat_adendum = $currentHistory;
                $mitra->save();

                // Pastikan register fisik aset mitra juga berstatus aktif / tersedia
                if ($mitra->astap_id) {
                    AstapRegister::where('astap_id', $mitra->astap_id)
                        ->where('status', 'Dikembalikan ke Rekanan')
                        ->update(['status' => 'Tersedia']);
                }

                $updatedCount++;
                $updatedRecords[] = [
                    'id'                  => $mitra->id,
                    'tanggal_selesai'     => $tglBaru,
                    'status_konsesi'      => 'Aktif',
                    'jumlah_extend'       => $nextExtendNo,
                ];
            }

            $tglBaruFmt = Carbon::parse($tglBaru)->format('d/m/Y');

            return response()->json([
                'success'         => true,
                'message'         => "Sebanyak {$updatedCount} aset rekanan mitra (Tabel 2) berhasil diperpanjang masa operasionalnya sampai {$tglBaruFmt}.",
                'updated_count'   => $updatedCount,
                'updated_records' => $updatedRecords,
            ]);
        });
    }

    /**
     * Hapus / Pindahkan Catatan Aset Kemitraan ke Pusat Pemulihan Data (Soft Delete)
     */
    public function destroy(Request $request, $id)
    {
        // 1. Cari catatan kemitraan (aktif maupun berdasarkan astap_id)
        $kemitraan = AstapKemitraan::find($id);
        if (!$kemitraan) {
            $kemitraan = AstapKemitraan::where('astap_id', $id)->first();
        }

        // 2. Jika tidak ada di tabel astap_kemitraans, cek apakah ada di tabel Astap atau AstapReklas (kasus reklasifikasi pending)
        $astap = $kemitraan ? $kemitraan->astap : Astap::find($id);
        $reklas = AstapReklas::where('astap_id', $id)
            ->orWhere('id', $id)
            ->get();

        if (!$kemitraan && !$astap && $reklas->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data Kemitraan tidak ditemukan.'], 404);
        }

        // Proteksi Opsi 1 (Strict Protection): Cek apakah objek pemanfaatan ini masih menampung aset aktif yang ditambahkan mitra
        $targetAstapId = $astap?->id ?: ($kemitraan?->astap_id ?: $id);
        if ($targetAstapId) {
            $activeLinkedCount = AstapKemitraan::where('objek_astap_id', $targetAstapId)
                ->where('is_deleted', 0)
                ->count();
            if ($activeLinkedCount > 0) {
                return response()->json([
                    'success' => false,
                    'is_blocked' => true,
                    'message' => "Objek pemanfaatan ini tidak dapat dihapus karena masih menampung {$activeLinkedCount} aset aktif yang ditambahkan oleh mitra rekanan di Tabel 2. Silakan hapus/batalkan aset mitra terkait terlebih dahulu demi akuntabilitas inventaris RSUD."
                ], 422);
            }
        }

        $alasanHapus = $request->input('alasan_hapus', 'Dihapus dari Kelola Kemitraan Aset');

        DB::transaction(function () use ($kemitraan, $astap, $reklas, $id, $alasanHapus) {
            $user = Auth::user();
            $deleterName = $user ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') : 'Administrator';
            $deleterId = $user?->id;
            $now = now();

            // 1. Soft delete catatan kemitraan jika ada
            if ($kemitraan) {
                $kemitraan->softDelete($alasanHapus);
            } else if ($astap) {
                // Jika baru berstatus reklasifikasi dan belum punya record kemitraan, buat record soft delete agar masuk recycle bin
                AstapKemitraan::create([
                    'astap_id'         => $astap->id,
                    'mitra_nama'       => '-',
                    'nomor_pks'        => 'Belum Ada PKS',
                    'tanggal_pks'      => $now,
                    'skema_kemitraan'  => 'Reklasifikasi',
                    'status_konsesi'   => 'Siap Dikerjasamakan',
                    'jumlah_volume'    => max(1, (int) $astap->jumlah_volume),
                    'satuan'           => $astap->satuan ?: 'Unit',
                    'nilai_aset'       => (float) $astap->total_realisasi,
                    'tahun'            => (int) ($astap->tahun_perolehan ?: date('Y')),
                    'triwulan'         => $astap->triwulan ?: 'TW I',
                    'tipe_kemitraan'   => 'dimanfaatkan',
                    'is_deleted'       => 1,
                    'deleted_at'       => $now,
                    'deleted_by'       => $deleterName,
                    'deleted_by_id'    => $deleterId,
                    'alasan_hapus'     => $alasanHapus,
                ]);
            }

            // 2. Soft delete aset ASTAP dan unit registernya
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
                    'alasan_hapus'  => $alasanHapus,
                ]);

                // Soft delete seluruh riwayat reklasifikasi terkait aset ini yang mengarah ke 1.5.2
                AstapReklas::where('astap_id', $astap->id)->update([
                    'is_deleted'    => 1,
                    'deleted_by'    => $deleterName,
                    'deleted_by_id' => $deleterId,
                    'deleted_at'    => $now,
                    'alasan_hapus'  => $alasanHapus,
                ]);
            }

            // 3. Jika ada reklas spesifik
            if ($reklas->isNotEmpty()) {
                foreach ($reklas as $rek) {
                    $rek->update([
                        'is_deleted'    => 1,
                        'deleted_by'    => $deleterName,
                        'deleted_by_id' => $deleterId,
                        'deleted_at'    => $now,
                        'alasan_hapus'  => $alasanHapus,
                    ]);
                }
            }

            // 4. Soft delete aset yang ditambahkan mitra jika objek pemanfaatan ini yang dihapus
            $targetAstapId = $astap?->id ?: ($kemitraan?->astap_id ?: $id);
            if ($targetAstapId) {
                $linkedDitambahkan = AstapKemitraan::where('objek_astap_id', $targetAstapId)
                    ->where('is_deleted', 0)
                    ->get();
                foreach ($linkedDitambahkan as $linked) {
                    $linked->softDelete('Objek Aset Pemanfaatan Terkait Dihapus');
                    if ($linked->astap) {
                        AstapRegister::where('astap_id', $linked->astap->id)->update([
                            'is_deleted'    => 1,
                            'deleted_by'    => $deleterName,
                            'deleted_by_id' => $deleterId,
                            'deleted_at'    => $now,
                        ]);
                        $linked->astap->update([
                            'is_deleted'    => 1,
                            'deleted_by'    => $deleterName,
                            'deleted_by_id' => $deleterId,
                            'deleted_at'    => $now,
                            'alasan_hapus'  => 'Objek Aset Pemanfaatan Terkait Dihapus',
                        ]);
                    }
                }
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

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
use Illuminate\Support\Facades\Storage;
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

        // KMT-04: Hitung peringatan masa konsesi yang akan berakhir
        $akanBerakhir30 = (clone $kpiBase)
            ->where('status_konsesi', 'Aktif')
            ->whereNotNull('tanggal_selesai')
            ->whereBetween('tanggal_selesai', [$today, $today->copy()->addDays(30)])
            ->count();

        $akanBerakhir7 = (clone $kpiBase)
            ->where('status_konsesi', 'Aktif')
            ->whereNotNull('tanggal_selesai')
            ->whereBetween('tanggal_selesai', [$today, $today->copy()->addDays(7)])
            ->count();

        // Hitung total aset yang dikembalikan ke rekanan & yang sudah jadi milik RSUD
        $totalDikembalikan = (clone $kpiBase)
            ->where('status_konsesi', 'Dikembalikan ke Rekanan')
            ->count();

        $totalMilikRsud = (clone $kpiBase)
            ->whereIn('status_konsesi', ['Selesai / Reklasifikasi', 'Selesai'])
            ->count();

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
            'totalDikembalikan',
            'totalMilikRsud',
            'akanBerakhir30',
            'akanBerakhir7',
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
     * KMT-03: Memperbarui Metadata PKS Kemitraan (Inline Edit dari Halaman Master)
     */
    public function update(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::find($id);
        if (!$kemitraan) {
            $kemitraan = AstapKemitraan::where('astap_id', $id)->firstOrFail();
        }

        $validated = $request->validate([
            'nomor_pks'       => 'nullable|string|max:150',
            'mitra_nama'      => 'required|string|max:255',
            'mitra_pimpinan'  => 'nullable|string|max:255',
            'mitra_alamat'    => 'nullable|string|max:500',
            'skema_kemitraan' => 'nullable|string|max:50',
            'tanggal_pks'     => 'nullable|date',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'jangka_waktu'    => 'nullable|string|max:100',
            'nilai_aset'      => 'nullable|numeric|min:0',
            'satuan'          => 'nullable|string|max:50',
            'keterangan'      => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated, $kemitraan, $request) {
            $kemitraan->mitra_nama = trim($validated['mitra_nama']);
            if (array_key_exists('nomor_pks', $validated)) {
                $kemitraan->nomor_pks = $validated['nomor_pks'] ? trim($validated['nomor_pks']) : $kemitraan->nomor_pks;
            }
            if (array_key_exists('mitra_pimpinan', $validated)) {
                $kemitraan->mitra_pimpinan = $validated['mitra_pimpinan'] ? trim($validated['mitra_pimpinan']) : null;
            }
            if (array_key_exists('mitra_alamat', $validated)) {
                $kemitraan->mitra_alamat = $validated['mitra_alamat'] ? trim($validated['mitra_alamat']) : null;
            }
            if (isset($validated['skema_kemitraan'])) {
                $kemitraan->skema_kemitraan = $validated['skema_kemitraan'];
            }
            if (array_key_exists('tanggal_pks', $validated)) {
                $kemitraan->tanggal_pks = $validated['tanggal_pks'];
            }
            if (array_key_exists('tanggal_mulai', $validated)) {
                $kemitraan->tanggal_mulai = $validated['tanggal_mulai'];
            }
            if (array_key_exists('tanggal_selesai', $validated)) {
                $kemitraan->tanggal_selesai = $validated['tanggal_selesai'];
            }
            if (isset($validated['nilai_aset'])) {
                $kemitraan->nilai_aset = (float) $validated['nilai_aset'];
            }
            if (isset($validated['satuan'])) {
                $kemitraan->satuan = $validated['satuan'];
            }
            if (array_key_exists('keterangan', $validated)) {
                $kemitraan->keterangan = $validated['keterangan'];
            }

            $kemitraan->save();

            // Sinkronisasi data ke ASTAP jika terhubung
            if ($kemitraan->astap_id && $kemitraan->astap) {
                $astap = $kemitraan->astap;
                $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
                
                $spec['nomor_pks']        = $kemitraan->nomor_pks;
                $spec['perjanjian_nomor'] = $kemitraan->nomor_pks;
                $spec['mitra_nama']       = $kemitraan->mitra_nama;
                $spec['mitra_pimpinan']   = $kemitraan->mitra_pimpinan;
                $spec['mitra_alamat']     = $kemitraan->mitra_alamat;
                $spec['skema_kemitraan']  = $kemitraan->skema_kemitraan;
                if ($kemitraan->tanggal_pks) {
                    $spec['tanggal_pks'] = is_string($kemitraan->tanggal_pks) ? $kemitraan->tanggal_pks : $kemitraan->tanggal_pks->format('Y-m-d');
                }
                if ($kemitraan->tanggal_mulai) {
                    $spec['tanggal_mulai'] = is_string($kemitraan->tanggal_mulai) ? $kemitraan->tanggal_mulai : $kemitraan->tanggal_mulai->format('Y-m-d');
                }
                if ($kemitraan->tanggal_selesai) {
                    $spec['tanggal_selesai'] = is_string($kemitraan->tanggal_selesai) ? $kemitraan->tanggal_selesai : $kemitraan->tanggal_selesai->format('Y-m-d');
                }
                if (!empty($validated['jangka_waktu'])) {
                    $spec['jangka_waktu'] = $validated['jangka_waktu'];
                }
                
                $astap->spesifikasi_json = $spec;
                if (isset($validated['nilai_aset']) && (float)$validated['nilai_aset'] > 0) {
                    $astap->total_realisasi = (float) $validated['nilai_aset'];
                }
                $astap->save();
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Metadata PKS '{$kemitraan->nomor_pks}' ({$kemitraan->mitra_nama}) berhasil diperbarui.",
                    'data'    => $kemitraan->fresh(['astap']),
                ]);
            }

            return redirect()->route('master.kemitraan')
                ->with('success', "Metadata PKS '{$kemitraan->nomor_pks}' berhasil diperbarui.");
        });
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
     * Terminasi Kemitraan: Kembalikan Aset ke Pihak Mitra / Rekanan (Jalur 1)
     */
    public function kembalikanKeMitra(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::find($id);
        if (!$kemitraan) {
            $kemitraan = AstapKemitraan::where('astap_id', $id)->firstOrFail();
        }

        $validated = $request->validate([
            'nomor_ba_terminasi'     => 'required|string|max:150',
            'tanggal_terminasi'      => 'required|date',
            'tanggal_ba_terminasi'   => 'nullable|date',
            'kondisi_akhir'          => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'pihak_penyerah_nama'    => 'required|string|max:255',
            'pihak_penyerah_jabatan' => 'nullable|string|max:255',
            'pihak_penerima_nama'    => 'required|string|max:255',
            'pihak_penerima_jabatan' => 'nullable|string|max:255',
            'catatan_terminasi'      => 'nullable|string|max:1000',
            'berkas_ba_terminasi'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        return DB::transaction(function () use ($request, $validated, $kemitraan) {
            $berkasPath = $kemitraan->berkas_ba_terminasi;
            if ($request->hasFile('berkas_ba_terminasi')) {
                $file = $request->file('berkas_ba_terminasi');
                $filename = 'BA_PENGEMBALIAN_' . $kemitraan->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $berkasPath = $file->storeAs('kemitraan/ba_terminasi', $filename, 'public');
            }

            $tglTerminasi = $validated['tanggal_terminasi'];
            $tglBa = !empty($validated['tanggal_ba_terminasi']) ? $validated['tanggal_ba_terminasi'] : $tglTerminasi;

            $kemitraan->update([
                'status_konsesi'         => 'Dikembalikan ke Rekanan',
                'jenis_terminasi'        => 'dikembalikan_ke_mitra',
                'nomor_ba_terminasi'     => trim($validated['nomor_ba_terminasi']),
                'tanggal_terminasi'      => $tglTerminasi,
                'tanggal_ba_terminasi'   => $tglBa,
                'berkas_ba_terminasi'    => $berkasPath,
                'kondisi_akhir'          => $validated['kondisi_akhir'],
                'pihak_penyerah_nama'    => trim($validated['pihak_penyerah_nama']),
                'pihak_penyerah_jabatan' => trim($validated['pihak_penyerah_jabatan'] ?? ''),
                'pihak_penerima_nama'    => trim($validated['pihak_penerima_nama']),
                'pihak_penerima_jabatan' => trim($validated['pihak_penerima_jabatan'] ?? ''),
                'catatan_terminasi'      => $validated['catatan_terminasi'] ?? null,
                'is_transferred_to_rsud' => false,
            ]);

            // Sinkronisasi status register fisik aset
            if ($kemitraan->astap_id) {
                AstapRegister::where('astap_id', $kemitraan->astap_id)
                    ->update([
                        'status'         => 'Dikembalikan ke Rekanan',
                        'kondisi'        => $validated['kondisi_akhir'],
                        'ruang_pemegang' => 'Telah Dikembalikan ke Rekanan (BA: ' . trim($validated['nomor_ba_terminasi']) . ')',
                    ]);

                // Simpan juga ke spesifikasi JSON astap
                if ($kemitraan->astap) {
                    $astap = $kemitraan->astap;
                    $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
                    $spec['terminasi'] = [
                        'jenis'              => 'dikembalikan_ke_mitra',
                        'nomor_ba'           => trim($validated['nomor_ba_terminasi']),
                        'tanggal_terminasi'  => $tglTerminasi,
                        'tanggal_ba'         => $tglBa,
                        'kondisi_akhir'      => $validated['kondisi_akhir'],
                        'pihak_penyerah'     => trim($validated['pihak_penyerah_nama']),
                        'pihak_penerima'     => trim($validated['pihak_penerima_nama']),
                        'catatan'            => $validated['catatan_terminasi'] ?? '',
                        'berkas_path'        => $berkasPath,
                        'processed_at'       => now()->toDateTimeString(),
                        'processed_by'       => Auth::user()?->name ?? 'Admin',
                    ];
                    $spec['kondisi'] = $validated['kondisi_akhir'];
                    $astap->spesifikasi_json = $spec;
                    $astap->save();
                }
            }

            // Jika objek pemanfaatan BMD RSUD (Induk) dimanfaatkan, lepas status pemanfaatannya
            if ($kemitraan->tipe_kemitraan === 'dimanfaatkan') {
                AstapKemitraan::where('objek_astap_id', $kemitraan->astap_id)
                    ->where('is_deleted', 0)
                    ->where('nomor_pks', $kemitraan->nomor_pks)
                    ->update(['status_konsesi' => 'Dikembalikan ke Rekanan']);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Aset kemitraan PKS '{$kemitraan->nomor_pks}' berhasil diproses dan dikembalikan ke pihak rekanan.",
                    'data'    => $kemitraan->fresh(['astap']),
                ]);
            }

            return redirect()->route('master.kemitraan')
                ->with('success', "Aset kemitraan '{$kemitraan->nomor_pks}' berhasil dikembalikan ke pihak rekanan.");
        });
    }

    /**
     * Terminasi Kemitraan: Serah Terima Aset Menjadi Milik Daerah RSUD (Jalur 2: Alih Milik / Hibah BGS/BSG)
     */
    public function serahTerimaRsud(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::find($id);
        if (!$kemitraan) {
            $kemitraan = AstapKemitraan::where('astap_id', $id)->firstOrFail();
        }

        $validated = $request->validate([
            'nomor_ba_terminasi'     => 'required|string|max:150',
            'tanggal_terminasi'      => 'required|date',
            'tanggal_ba_terminasi'   => 'nullable|date',
            'nilai_perolehan_rsud'   => 'required|numeric|min:0',
            'target_kib_reklas'      => 'required|string|in:KIB_A,KIB_B,KIB_C,KIB_D,KIB_E',
            'target_kode_108'        => 'nullable|string|max:50',
            'kondisi_akhir'          => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'pihak_penyerah_nama'    => 'required|string|max:255',
            'pihak_penyerah_jabatan' => 'nullable|string|max:255',
            'pihak_penerima_nama'    => 'required|string|max:255',
            'pihak_penerima_jabatan' => 'nullable|string|max:255',
            'ruangan_tujuan_id'      => 'nullable|exists:units,id',
            'catatan_terminasi'      => 'nullable|string|max:1000',
            'berkas_ba_terminasi'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        return DB::transaction(function () use ($request, $validated, $kemitraan) {
            $berkasPath = $kemitraan->berkas_ba_terminasi;
            if ($request->hasFile('berkas_ba_terminasi')) {
                $file = $request->file('berkas_ba_terminasi');
                $filename = 'BA_SERAH_TERIMA_RSUD_' . $kemitraan->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $berkasPath = $file->storeAs('kemitraan/ba_terminasi', $filename, 'public');
            }

            $tglTerminasi = $validated['tanggal_terminasi'];
            $tglBa = !empty($validated['tanggal_ba_terminasi']) ? $validated['tanggal_ba_terminasi'] : $tglTerminasi;

            $kemitraan->update([
                'status_konsesi'         => 'Selesai / Reklasifikasi',
                'jenis_terminasi'        => 'serah_terima_rsud',
                'nomor_ba_terminasi'     => trim($validated['nomor_ba_terminasi']),
                'tanggal_terminasi'      => $tglTerminasi,
                'tanggal_ba_terminasi'   => $tglBa,
                'berkas_ba_terminasi'    => $berkasPath,
                'kondisi_akhir'          => $validated['kondisi_akhir'],
                'pihak_penyerah_nama'    => trim($validated['pihak_penyerah_nama']),
                'pihak_penyerah_jabatan' => trim($validated['pihak_penyerah_jabatan'] ?? ''),
                'pihak_penerima_nama'    => trim($validated['pihak_penerima_nama']),
                'pihak_penerima_jabatan' => trim($validated['pihak_penerima_jabatan'] ?? ''),
                'catatan_terminasi'      => $validated['catatan_terminasi'] ?? null,
                'is_transferred_to_rsud' => true,
                'target_kib_reklas'      => $validated['target_kib_reklas'],
                'target_kode_108'        => $validated['target_kode_108'] ?? null,
                'nilai_perolehan_rsud'   => (float) $validated['nilai_perolehan_rsud'],
            ]);

            // Sinkronisasi status register fisik aset
            if ($kemitraan->astap_id) {
                $updateReg = [
                    'status'  => 'Tersedia',
                    'kondisi' => $validated['kondisi_akhir'],
                ];
                if (!empty($validated['ruangan_tujuan_id'])) {
                    $updateReg['unit_id'] = $validated['ruangan_tujuan_id'];
                    $unitNama = Unit::find($validated['ruangan_tujuan_id'])?->nama;
                    if ($unitNama) {
                        $updateReg['ruang_pemegang'] = $unitNama;
                    }
                }
                AstapRegister::where('astap_id', $kemitraan->astap_id)->update($updateReg);

                // Update data astap
                if ($kemitraan->astap) {
                    $astap = $kemitraan->astap;
                    $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
                    $spec['terminasi'] = [
                        'jenis'                => 'serah_terima_rsud',
                        'nomor_ba'             => trim($validated['nomor_ba_terminasi']),
                        'tanggal_terminasi'    => $tglTerminasi,
                        'tanggal_ba'           => $tglBa,
                        'nilai_perolehan_rsud' => (float) $validated['nilai_perolehan_rsud'],
                        'target_kib'           => $validated['target_kib_reklas'],
                        'target_kode_108'      => $validated['target_kode_108'] ?? '',
                        'kondisi_akhir'        => $validated['kondisi_akhir'],
                        'pihak_penyerah'       => trim($validated['pihak_penyerah_nama']),
                        'pihak_penerima'       => trim($validated['pihak_penerima_nama']),
                        'catatan'              => $validated['catatan_terminasi'] ?? '',
                        'berkas_path'          => $berkasPath,
                        'processed_at'         => now()->toDateTimeString(),
                        'processed_by'         => Auth::user()?->name ?? 'Admin',
                    ];
                    $spec['kondisi'] = $validated['kondisi_akhir'];
                    if (!empty($validated['ruangan_tujuan_id'])) {
                        $spec['unit_id'] = $validated['ruangan_tujuan_id'];
                    }
                    $astap->spesifikasi_json = $spec;
                    $astap->save();
                }
            }

            // Jika objek pemanfaatan BMD RSUD (Induk) dimanfaatkan, lepas status pemanfaatannya
            if ($kemitraan->tipe_kemitraan === 'dimanfaatkan') {
                AstapKemitraan::where('objek_astap_id', $kemitraan->astap_id)
                    ->where('is_deleted', 0)
                    ->where('nomor_pks', $kemitraan->nomor_pks)
                    ->update(['status_konsesi' => 'Selesai / Reklasifikasi']);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Aset kemitraan PKS '{$kemitraan->nomor_pks}' berhasil diserahterimakan dan resmi menjadi Milik RSUD Koesnandi.",
                    'data'    => $kemitraan->fresh(['astap']),
                ]);
            }

            return redirect()->route('master.kemitraan')
                ->with('success', "Aset kemitraan '{$kemitraan->nomor_pks}' berhasil diserahterimakan menjadi Milik RSUD.");
        });
    }

    /**
     * Batalkan Pengembalian Aset ke Mitra Rekanan (Reset Status Terminasi)
     */
    public function batalKembalikanKeMitra(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::find($id);
        if (!$kemitraan) {
            $kemitraan = AstapKemitraan::where('astap_id', $id)->firstOrFail();
        }

        return DB::transaction(function () use ($kemitraan, $request) {
            // Tentukan status kembalinya: apakah tanggal akhir masih di masa depan
            $statusSemula = 'Konsesi Berakhir';
            if (!empty($kemitraan->tanggal_akhir)) {
                $tglAkhir = Carbon::parse($kemitraan->tanggal_akhir);
                if ($tglAkhir->isFuture()) {
                    $statusSemula = 'Aktif';
                }
            }

            // Hapus file berkas BA terminasi jika ada di storage
            if ($kemitraan->berkas_ba_terminasi && Storage::disk('public')->exists($kemitraan->berkas_ba_terminasi)) {
                Storage::disk('public')->delete($kemitraan->berkas_ba_terminasi);
            }

            $kemitraan->update([
                'status_konsesi'         => $statusSemula,
                'jenis_terminasi'        => null,
                'nomor_ba_terminasi'     => null,
                'tanggal_terminasi'      => null,
                'tanggal_ba_terminasi'   => null,
                'berkas_ba_terminasi'    => null,
                'kondisi_akhir'          => null,
                'pihak_penyerah_nama'    => null,
                'pihak_penyerah_jabatan' => null,
                'pihak_penerima_nama'    => null,
                'pihak_penerima_jabatan' => null,
                'catatan_terminasi'      => null,
                'is_transferred_to_rsud' => false,
            ]);

            // Sinkronisasi status register fisik aset
            if ($kemitraan->astap_id) {
                AstapRegister::where('astap_id', $kemitraan->astap_id)
                    ->update([
                        'status'         => 'Kemitraan',
                        'ruang_pemegang' => 'Kemitraan (' . ($kemitraan->mitra_nama ?: 'Mitra Rekanan') . ')',
                    ]);

                if ($kemitraan->astap) {
                    $astap = $kemitraan->astap;
                    $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
                    unset($spec['terminasi']);
                    $astap->spesifikasi_json = $spec;
                    $astap->save();
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pengembalian aset kemitraan PKS '{$kemitraan->nomor_pks}' berhasil dibatalkan. Status aset dipulihkan menjadi '{$statusSemula}'.",
                    'data'    => $kemitraan->fresh(['astap']),
                ]);
            }

            return redirect()->route('master.kemitraan')
                ->with('success', "Pengembalian aset kemitraan '{$kemitraan->nomor_pks}' berhasil dibatalkan.");
        });
    }

    /**
     * Batalkan Kerja Sama Kemitraan (Status Dibatalkan + Input Alasan Pembatalan)
     */
    public function batalkanKemitraan(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::find($id);
        if (!$kemitraan) {
            $kemitraan = AstapKemitraan::where('astap_id', $id)
                ->orWhere('objek_astap_id', $id)
                ->first();
        }

        if (!$kemitraan) {
            return response()->json([
                'success' => false,
                'message' => 'Data kerja sama kemitraan tidak ditemukan (ID: ' . $id . ').'
            ], 404);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'alasan_batal' => 'required|string|max:1000'
        ], [
            'alasan_batal.required' => 'Alasan pembatalan kemitraan wajib diisi.',
            'alasan_batal.max'      => 'Alasan pembatalan maksimal 1000 karakter.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        return DB::transaction(function () use ($kemitraan, $request) {
            $alasan = trim($request->input('alasan_batal'));
            $user = Auth::user();
            $userName = $user ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') : 'Administrator';
            $now = now();

            $kemitraan->update([
                'status_konsesi' => 'Dibatalkan',
                'alasan_batal'   => $alasan,
                'dibatalkan_at'  => $now,
                'dibatalkan_by'  => $userName,
            ]);

            // Jika aset dimanfaatkan (BMD RSUD):
            // Objek aset BMD dikembalikan statusnya ke Tersedia (KIB asalnya), riwayat kemitraan tetap tercatat
            if ($kemitraan->tipe_kemitraan === 'dimanfaatkan') {
                $targetAstapId = $kemitraan->objek_astap_id ?: $kemitraan->astap_id;
                if ($targetAstapId) {
                    AstapRegister::where('astap_id', $targetAstapId)
                        ->update([
                            'status' => 'Tersedia'
                        ]);
                }
                if ($kemitraan->astap_id && $kemitraan->astap_id != $targetAstapId) {
                    AstapRegister::where('astap_id', $kemitraan->astap_id)
                        ->update([
                            'status' => 'Tersedia'
                        ]);
                }

                // Aset yang ditambahkan mitra yang tertaut pada objek pemanfaatan ini juga otomatis ikut dibatalkan
                AstapKemitraan::where('objek_astap_id', $targetAstapId)
                    ->where('id', '!=', $kemitraan->id)
                    ->where('is_deleted', 0)
                    ->where('status_konsesi', '!=', 'Dikembalikan ke Rekanan')
                    ->update([
                        'status_konsesi' => 'Dibatalkan',
                        'alasan_batal'   => 'Dibatalkan otomatis mengikuti pembatalan objek pemanfaatan BMD RSUD: ' . $alasan,
                        'dibatalkan_at'  => $now,
                        'dibatalkan_by'  => $userName,
                    ]);
            } else {
                // Untuk aset yang ditambahkan mitra:
                if ($kemitraan->astap_id) {
                    AstapRegister::where('astap_id', $kemitraan->astap_id)
                        ->update([
                            'status' => 'Dibatalkan'
                        ]);
                }
            }

            // Catat ke spesifikasi json astap sebagai riwayat audit
            if ($kemitraan->astap) {
                $astap = $kemitraan->astap;
                $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
                $spec['pembatalan_kemitraan'] = [
                    'status'        => 'Dibatalkan',
                    'alasan'        => $alasan,
                    'dibatalkan_at' => $now->toDateTimeString(),
                    'dibatalkan_by' => $userName,
                ];
                $astap->spesifikasi_json = $spec;
                $astap->save();
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Kerja sama kemitraan PKS '{$kemitraan->nomor_pks}' berhasil dibatalkan.",
                    'data'    => $kemitraan->fresh(['astap']),
                ]);
            }

            return redirect()->route('master.kemitraan')
                ->with('success', "Kerja sama kemitraan PKS '{$kemitraan->nomor_pks}' berhasil dibatalkan.");
        });
    }

    /**
     * Pulihkan Status Kemitraan yang Dibatalkan
     */
    public function batalPembatalanKemitraan(Request $request, $id)
    {
        $kemitraan = AstapKemitraan::find($id);
        if (!$kemitraan) {
            $kemitraan = AstapKemitraan::where('astap_id', $id)
                ->orWhere('objek_astap_id', $id)
                ->first();
        }

        if (!$kemitraan) {
            return response()->json([
                'success' => false,
                'message' => 'Data kerja sama kemitraan tidak ditemukan (ID: ' . $id . ').'
            ], 404);
        }

        return DB::transaction(function () use ($kemitraan, $request) {
            $statusSemula = 'Konsesi Berakhir';
            if (!empty($kemitraan->tanggal_selesai)) {
                $tglAkhir = Carbon::parse($kemitraan->tanggal_selesai);
                if ($tglAkhir->isFuture()) {
                    $statusSemula = 'Aktif';
                }
            }

            $kemitraan->update([
                'status_konsesi' => $statusSemula,
                'alasan_batal'   => null,
                'dibatalkan_at'  => null,
                'dibatalkan_by'  => null,
            ]);

            if ($kemitraan->tipe_kemitraan === 'dimanfaatkan') {
                $targetAstapId = $kemitraan->objek_astap_id ?: $kemitraan->astap_id;
                if ($targetAstapId) {
                    AstapRegister::where('astap_id', $targetAstapId)->update(['status' => 'Kemitraan']);
                }
                if ($kemitraan->astap_id && $kemitraan->astap_id != $targetAstapId) {
                    AstapRegister::where('astap_id', $kemitraan->astap_id)->update(['status' => 'Kemitraan']);
                }
            } else {
                if ($kemitraan->astap_id) {
                    AstapRegister::where('astap_id', $kemitraan->astap_id)->update(['status' => 'Kemitraan']);
                }
            }

            if ($kemitraan->astap) {
                $astap = $kemitraan->astap;
                $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json ?? '[]', true) ?: []);
                unset($spec['pembatalan_kemitraan']);
                $astap->spesifikasi_json = $spec;
                $astap->save();
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pembatalan kemitraan berhasil dicabut. Status dipulihkan ke '{$statusSemula}'.",
                    'data'    => $kemitraan->fresh(['astap']),
                ]);
            }

            return redirect()->route('master.kemitraan')
                ->with('success', "Pembatalan kemitraan berhasil dicabut.");
        });
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

        // Validasi: tanggal berakhir konsesi baru tidak boleh di bawah atau sama dengan tanggal berakhir konsesi lama
        if ($kemitraan->tanggal_selesai) {
            $tglSelesaiLama = Carbon::parse($kemitraan->tanggal_selesai)->startOfDay();
            $tglSelesaiBaru = Carbon::parse($request->input('tanggal_selesai_baru'))->startOfDay();

            if ($tglSelesaiBaru->lte($tglSelesaiLama)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal berakhir konsesi baru harus lebih besar dari tanggal konsesi berakhir saat ini (' . $tglSelesaiLama->format('d/m/Y') . ').'
                ], 422);
            }
        }

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

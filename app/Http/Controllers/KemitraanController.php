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

        // Sinkronisasi status konsesi: Jika tanggal_selesai sudah lewat, ubah status 'Aktif' menjadi 'Konsesi Berakhir'
        $today = Carbon::today();
        $expiredKemitraans = AstapKemitraan::where('is_deleted', 0)
            ->where('status_konsesi', 'Aktif')
            ->whereNotNull('tanggal_selesai')
            ->where('tanggal_selesai', '<', $today)
            ->get();

        foreach ($expiredKemitraans as $exp) {
            $exp->update(['status_konsesi' => 'Konsesi Berakhir']);
        }

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
            'status_konsesi' => 'required|string|in:Aktif,Konsesi Berakhir,Selesai,Selesai / Reklasifikasi,Dihentikan'
        ]);

        $newStatus = $request->input('status_konsesi');
        $kemitraan->status_konsesi = $newStatus;
        $kemitraan->save();

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

    /**
     * Cetak Draf Dokumen Resmi BAST Pemanfaatan BMD Kemitraan (Format Kedinasan A4)
     */
    public function cetakBast(Request $request, $id)
    {
        Carbon::setLocale('id');

        // 1. Cek apakah ID merujuk ke record tabel astap_kemitraans
        $kemitraan = AstapKemitraan::with([
            'astap.jenisAstap', 
            'astap.registers.unit', 
            'astap.unit', 
            'astap.reklas',
            'objekRegister.astap.reklas', 
            'objekAstap.reklas'
        ])->find($id);
        $astap = null;

        if ($kemitraan) {
            $astap = $kemitraan->astap ?: ($kemitraan->objekRegister?->astap ?: $kemitraan->objekAstap);
        } else {
            // 2. Jika bukan ID kemitraan, cari dari tabel astaps (misal: aset hasil reklasifikasi yang belum ada PKS)
            $astap = Astap::with(['jenisAstap', 'registers.unit', 'kemitraan', 'unit', 'reklas'])->find($id);
            if ($astap) {
                $kemitraan = $astap->kemitraan;
            }
        }

        // 3. Fallback pencarian kemitraan berdasarkan relasi astap_id / objek_astap_id
        if (!$kemitraan && $astap) {
            $kemitraan = AstapKemitraan::where('astap_id', $astap->id)
                ->orWhere('objek_astap_id', $astap->id)
                ->first();
        }

        if (!$astap) {
            abort(404, 'Data aset kemitraan atau objek BAST tidak ditemukan.');
        }

        $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap->spesifikasi_json, true) ?? []);

        // Objek Aset Terkait (Jika ada aset tanah RSUD yang disewakan / dimanfaatkan)
        $objekAset = null;
        if (!empty($spec['objek_astap_id'])) {
            $objekAset = Astap::with(['jenisAstap', 'registers'])->find($spec['objek_astap_id']);
        }
        if (!$objekAset && !empty($kemitraan?->objek_astap_id)) {
            $objekAset = Astap::with(['jenisAstap', 'registers'])->find($kemitraan->objek_astap_id);
        }
        if (!$objekAset && !empty($kemitraan?->objek_register_id)) {
            $regObj = AstapRegister::with('astap.jenisAstap')->find($kemitraan->objek_register_id);
            $objekAset = $regObj?->astap;
        }
        if (!$objekAset) {
            $objekAset = $astap;
        }

        $objekSpec = is_array($objekAset?->spesifikasi_json) ? $objekAset->spesifikasi_json : (json_decode($objekAset?->spesifikasi_json, true) ?? []);

        // Tanggal BAST & Hari
        $tglPks = $kemitraan?->tanggal_pks ?: ($spec['tanggal_pks'] ?? ($astap->bast_dokumen_tanggal ?? now()));
        $carbonTgl = Carbon::parse($tglPks);
        $hariTgl = $carbonTgl->isoFormat('dddd');
        $tglFormatted = $carbonTgl->isoFormat('D MMMM Y');
        $tahun = $carbonTgl->format('Y');

        // Pejabat Pihak Pertama (RSUD Dr. H. Koesnandi)
        $pihakSatu = [
            'nama'        => 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR',
            'nip'         => '19771002 200604 1 006',
            'pangkat'     => 'Pembina Tingkat I (IV/b)',
            'jabatan'     => 'Direktur RSUD dr. H. Koesnandi Bondowoso',
            'instansi'    => 'RSUD dr. H. Koesnandi Kabupaten Bondowoso',
            'alamat'      => 'Jl. Kapten Piere Tendean No. 1, Bondowoso',
        ];

        // Pejabat Pihak Kedua (Mitra)
        $pihakDua = [
            'perusahaan'  => $kemitraan?->mitra_nama ?: ($spec['mitra_nama'] ?? 'Mitra Kerja Sama'),
            'pimpinan'    => $kemitraan?->mitra_pimpinan ?: ($kemitraan?->pimpinan_mitra ?: ($spec['mitra_pimpinan'] ?? 'Pimpinan / Direktur Rekanan')),
            'alamat'      => $kemitraan?->mitra_alamat ?: ($kemitraan?->alamat_mitra ?: ($spec['mitra_alamat'] ?? 'Alamat Domisili Mitra')),
            'jabatan'     => 'Pimpinan / Kuasa Direksi',
        ];

        // Pejabat Pengurus Barang Pengguna (Saksi / Mengetahui)
        $pengurusBarang = [
            'nama'        => 'BUDI HARTONO, S.Sos',
            'nip'         => '19760229 200801 1 010',
            'jabatan'     => 'Pengurus Barang Pengguna RSUD dr. H. Koesnandi',
        ];

        // Rincian Objek Fisik (Spesifikasi Tanah KIB A atau Gedung Bangunan)
        $tanahItems = $spec['tanah_items'] ?? ($objekSpec['tanah_items'] ?? []);
        $luasTotal = (float) ($spec['luas_m2'] ?? ($spec['tanah_luas_m2'] ?? ($objekSpec['luas_m2'] ?? ($objekSpec['tanah_luas_m2'] ?? 0))));
        $sertifikatNo = $spec['sertifikat_no'] ?? ($spec['tanah_sertifikat_no'] ?? ($objekSpec['sertifikat_no'] ?? ($objekSpec['tanah_sertifikat_no'] ?? '-')));
        $hakTanah = $spec['hak_tanah'] ?? ($spec['tanah_hak'] ?? ($objekSpec['hak_tanah'] ?? ($objekSpec['tanah_hak'] ?? 'Hak Pakai')));

        // Nomor Surat BAST
        $nomorPks = $kemitraan?->nomor_pks ?: ($spec['nomor_pks'] ?? ($spec['perjanjian_nomor'] ?? ($astap->bast_dokumen_nomor ?: '000.2.3.2/BAST-KSO/430.10.7/' . $tahun)));
        $nomorBast = '000.2.3.2/BAST-KMT/' . ($kemitraan?->id ?: $astap->id) . '/430.10.7/' . $tahun;

        // Resolusi Nama Fisik Asli Barang (jika pernah direklasifikasi dari aset fisik)
        $reklasHistory = $astap?->reklas?->sortByDesc('id')->first() ?: ($objekAset?->reklas?->sortByDesc('id')->first());
        $namaFisikAsli = null;
        if (!empty($astap?->nama_barang) && !str_starts_with(strtolower($astap->nama_barang), 'kerja sama pemanfaatan') && !str_starts_with(strtolower($astap->nama_barang), 'bangun guna serah')) {
            $namaFisikAsli = $astap->nama_barang;
        } elseif ($reklasHistory && !empty($reklasHistory->asal_nama)) {
            $namaFisikAsli = $reklasHistory->asal_nama;
        } elseif (!empty($objekAset?->nama_barang) && !str_starts_with(strtolower($objekAset->nama_barang), 'kerja sama pemanfaatan')) {
            $namaFisikAsli = $objekAset->nama_barang;
        } else {
            $namaFisikAsli = $spec['mesin_items'][0]['mesin_nama_barang'] ?? ($spec['tanah_items'][0]['tanah_nama_barang'] ?? ($astap?->nama_barang ?: 'Objek Aset BMD RSUD'));
        }

        return redirect()->route('master.kemitraan')
            ->with('info', 'Dokumen resmi BAST dapat dicetak langsung melalui tombol Cetak Resmi BAST pada menu Detail Aset.');
    }
}

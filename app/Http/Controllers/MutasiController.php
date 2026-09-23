<?php

namespace App\Http\Controllers;

use App\Models\AstapMutasi;
use App\Models\AstapMutasiRegister;
use App\Models\AstapRegister;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MutasiController extends Controller
{

    /**
     * Dapatkan daftar ID register aset yang saat ini sedang dalam proses pengajuan mutasi aktif (pending).
     */
    public static function getLockedRegisterIds(?int $excludeMutasiId = null): array
    {
        $query = AstapMutasi::where('is_deleted', 0)
            ->whereNotIn('status', ['Disetujui Admin (Selesai)', 'Ditolak']);
        if ($excludeMutasiId) {
            $query->where('id', '!=', $excludeMutasiId);
        }
        $pendingMutasiIds = $query->pluck('id')->toArray();

        if (empty($pendingMutasiIds)) {
            return [];
        }

        $regIds = AstapMutasiRegister::whereIn('astap_mutasi_id', $pendingMutasiIds)
            ->pluck('astap_register_id')
            ->filter()
            ->unique()
            ->toArray();

        $legacyIds = AstapMutasi::whereIn('id', $pendingMutasiIds)
            ->whereNotNull('astap_register_id')
            ->pluck('astap_register_id')
            ->filter()
            ->unique()
            ->toArray();

        return array_values(array_unique(array_merge($regIds, $legacyIds)));
    }

    /**
     * Tampilkan daftar mutasi aset.
     */
    public function index()
    {
        $rawMutasis = AstapMutasi::with(['items.register.astap', 'items.register.unit'])
            ->latest('tanggal_mutasi')
            ->latest('id')
            ->get();

        $mutasis = $rawMutasis->map(function ($m) {
            $firstItem = $m->items->first();
            $itemCount = $m->items->count();
            $firstRegister = $firstItem?->register ?? $m->register;
            $firstAstap = $firstRegister?->astap;

            $itemSummary = $firstAstap?->nama_barang ?? '(Aset Tanpa Nama)';
            if ($itemCount > 1) {
                $itemSummary .= ' (+' . ($itemCount - 1) . ' barang lainnya)';
            }

            $itemsMapped = $m->items->map(function ($it, $idx) {
                $r = $it->register;
                return [
                    'no'          => $idx + 1,
                    'register_id' => $it->astap_register_id,
                    'nibar'       => $r?->nibar ?? '-',
                    'nama_barang' => $r?->astap?->nama_barang ?? '-',
                    'kode_108'    => $r?->astap?->kode_108 ?? ($r?->kode_108 ?? '-'),
                    'kondisi'     => $it->kondisi ?? ($r?->kondisi ?? 'Baik'),
                    'kategori'    => $r?->astap?->category ?? 'ASTAP',
                    'satuan'      => $r?->astap?->satuan ?? 'Unit',
                    'volume'      => 1,
                ];
            })->values()->toArray();

            // Jika items kosong (legacy single), buat 1 item fallback
            if (empty($itemsMapped) && $firstRegister) {
                $itemsMapped = [[
                    'no'          => 1,
                    'register_id' => $firstRegister->id,
                    'nibar'       => $firstRegister->nibar ?? '-',
                    'nama_barang' => $firstAstap?->nama_barang ?? '-',
                    'kode_108'    => $firstAstap?->kode_108 ?? ($firstRegister->kode_108 ?? '-'),
                    'kondisi'     => $firstRegister->kondisi ?? 'Baik',
                    'kategori'    => $firstAstap?->category ?? 'ASTAP',
                    'satuan'      => $firstAstap?->satuan ?? 'Unit',
                    'volume'      => 1,
                ]];
                $itemCount = 1;
            }

            $isPending = !in_array($m->status, ['Disetujui Admin (Selesai)', 'Ditolak']);

            return [
                'id'                      => $m->id,
                'is_deleted'              => (int) ($m->is_deleted ?? 0),
                'deleted_by'              => $m->deleted_by,
                'deleted_at'              => $m->deleted_at ? $m->deleted_at->translatedFormat('d M Y, H:i') . ' WIB' : null,
                'deleted_at_raw'          => $m->deleted_at ? $m->deleted_at->toIso8601String() : null,
                'kode'                    => $m->nomor_bamb,
                'bast_nomor'              => $m->nomor_bamb,
                'jenis'                   => $m->jenis_mutasi,
                'jenis_mutasi'            => $m->jenis_mutasi,
                'nama'                    => $itemSummary,
                'item_count'              => $itemCount,
                'items'                   => $itemsMapped,
                'kode_barang'             => $firstRegister?->nibar ?? '-',
                'kode_108'                => $firstAstap?->kode_108 ?? ($firstRegister?->kode_108 ?? '-'),
                'kondisi'                 => $firstItem?->kondisi ?? ($firstRegister?->kondisi ?? 'Baik'),
                'asal'                    => $m->ruangan_asal,
                'tujuan'                  => $m->ruangan_tujuan,
                'tgl'                     => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('d M Y') : '-',
                'tgl_raw'                 => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y-m-d') : null,
                'pemohon'                 => $m->penanggung_jawab_asal,
                'penerima_pj'             => $m->penanggung_jawab_tujuan,
                'pj_asal_nama'            => $m->penanggung_jawab_asal,
                'pj_asal_nip'             => '-',
                'pj_asal_jabatan'         => 'Penanggung Jawab ' . $m->ruangan_asal,
                'pj_tujuan_nama'          => $m->penanggung_jawab_tujuan,
                'pj_tujuan_nip'           => '-',
                'pj_tujuan_jabatan'       => 'Penanggung Jawab ' . $m->ruangan_tujuan,
                'persetujuan_pengirim'    => (bool) $m->persetujuan_pengirim,
                'persetujuan_penerima'    => (bool) $m->persetujuan_penerima,
                'persetujuan_admin'       => (bool) $m->persetujuan_admin,
                'status'                  => $m->status,
                'keterangan'              => $m->alasan_mutasi,
                'catatan_penerima'        => $m->catatan_penerima,
                'alasan_penolakan'        => $m->alasan_penolakan,
                'hari'                    => $m->tanggal_mutasi ? $m->tanggal_mutasi->translatedFormat('l') : 'Hari ini',
                'tanggal_angka'           => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('d') : date('d'),
                'bulan'                   => $m->tanggal_mutasi ? $m->tanggal_mutasi->translatedFormat('F') : date('F'),
                'tahun'                   => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y') : date('Y'),
                'created_at_formatted'    => $m->created_at ? $m->created_at->translatedFormat('d M Y, H:i') : '-',
                'expires_at'              => null,
                'expires_at_formatted'    => null,
                'is_pending'              => $isPending,
                'is_expired'              => false,
                'sisa_waktu'              => null,
                'sisa_menit'              => 0,
            ];
        });

        $units = Unit::orderBy('nama')->get();

        return view('pages.mutasi_aset', compact('mutasis', 'units'));
    }

    /**
     * Tampilkan katalog mutasi eksternal (Pelimpahan SKPD / Antar-OPD).
     * Data ditarik langsung dari pendaftaran aset melalui Form Mutasi Masuk (Pelimpahan SKPD).
     */
    public function eksternal()
    {
        $dbMutasis = \App\Models\Astap::where('is_deleted', 0)
            ->where(function ($q) {
                $q->whereIn('sumber_dana', ['pelimpahan_skpd', 'mutasi_masuk'])
                  ->orWhereNotNull('mutasi_nomor_bamb')
                  ->orWhereNotNull('mutasi_asal');
            })
            ->with(['pelimpahanSkpd', 'unit', 'registers', 'jenisAstap'])
            ->latest('id')
            ->get();

        $mutasiEksternals = $dbMutasis->map(function ($astap, $index) {
            // Nomor BAST / BAMB
            $nomorBamb = $astap->pelimpahanSkpd?->nomor_bamb 
                ?: ($astap->mutasi_nomor_bamb 
                ?: ($astap->bast_dokumen_nomor ?: 'BAMB-SKPD-' . str_pad($astap->id, 4, '0', STR_PAD_LEFT)));

            // Tanggal
            $tglRaw = $astap->mutasi_tanggal 
                ?: ($astap->pelimpahanSkpd?->tanggal_bamb 
                ?: ($astap->bast_dokumen_tanggal ?: ($astap->created_at ? $astap->created_at->format('Y-m-d') : '')));
            
            $tglFormatted = '-';
            if (!empty($tglRaw)) {
                try {
                    $tglFormatted = \Carbon\Carbon::parse($tglRaw)->locale('id')->isoFormat('D MMM Y');
                } catch (\Throwable $e) {
                    $tglFormatted = (string) $tglRaw;
                }
            }

            // OPD Pengirim Luar (Asal)
            $opdAsal = $astap->pelimpahanSkpd?->skpd_asal 
                ?: ($astap->mutasi_asal ?: 'SKPD / Instansi Luar');

            // Ruangan / Unit Penempatan di RSUD (Tujuan)
            $ruangRSUD = $astap->unit?->nama ?: ($astap->alamat_barang ?: 'Gudang/Ruangan RSUD');
            $opdTujuan = 'RSUD dr. H. Koesnadi (' . $ruangRSUD . ')';

            // PPK / Pejabat Penerima RSUD
            $pjNama = $astap->ppk_nama ?: ($astap->spesifikasi_json['ppk_nama'] ?? 'Pengurus Barang RSUD');
            $pjNip  = $astap->ppk_nip ?: ($astap->spesifikasi_json['ppk_nip'] ?? '-');

            // Klasifikasi Kode 108
            $kode108 = $astap->kode_108 ?: ($astap->jenisAstap?->sub_sub_rincian_objek ?: ($astap->jenisAstap?->jenis ?: '-'));

            // Daftar registers / satuan barang
            $items = [];
            if ($astap->registers && $astap->registers->isNotEmpty()) {
                foreach ($astap->registers as $idx => $reg) {
                    $items[] = [
                        'no'          => $idx + 1,
                        'nama_barang' => $astap->nama_barang,
                        'nibar'       => $reg->nibar ?: '-',
                        'kode_108'    => $kode108,
                        'kondisi'     => $reg->kondisi ?: ($astap->spesifikasi_json['kondisi'] ?? 'Baik'),
                        'satuan'      => $astap->satuan ?: 'Unit',
                        'volume'      => 1,
                    ];
                }
            } else {
                $items[] = [
                    'no'          => 1,
                    'nama_barang' => $astap->nama_barang,
                    'nibar'       => '-',
                    'kode_108'    => $kode108,
                    'kondisi'     => $astap->spesifikasi_json['kondisi'] ?? 'Baik',
                    'satuan'      => $astap->satuan ?: 'Unit',
                    'volume'      => (int) ($astap->jumlah_volume ?: 1),
                ];
            }

            $itemCount = count($items);
            $firstNibar = ($items[0]['nibar'] !== '-') ? $items[0]['nibar'] : ($kode108 ?: '1.3.2.00.00.00');
            $nilaiReal = (float) ($astap->total_realisasi ?: ($astap->pelimpahanSkpd?->nilai_perolehan ?: 0));
            $tahunMasuk = (string) ($astap->tahun_perolehan ?: (date('Y', strtotime($tglRaw ?: 'now'))));
            $volAset = (int) ($astap->jumlah_volume ?: ($itemCount ?: 1));

            return [
                'id'                      => $astap->id,
                'is_deleted'              => (int) $astap->is_deleted,
                'kode'                    => $nomorBamb,
                'jenis'                   => 'Transfer Antar-OPD',
                'kategori_label'          => 'Pelimpahan SKPD (Mutasi Masuk)',
                'nama'                    => $astap->nama_barang . ($itemCount > 1 ? " (+{$itemCount} unit)" : ''),
                'nama_murni'              => $astap->nama_barang,
                'nama_barang'             => $astap->nama_barang,
                'category'                => $astap->category ?: 'KIB B',
                'is_extracomtable'        => (bool) $astap->is_extracomtable,
                'is_reklas'               => (bool) $astap->is_reklas,
                'jenis_reklas'            => $astap->jenis_reklas,
                'sumber_dana'             => $astap->sumber_dana ?: 'pelimpahan_skpd',
                'sumber_dana_raw'         => $astap->sumber_dana ?: 'pelimpahan_skpd',
                'jenis_aset_nama'         => $astap->jenisAstap?->nama_jenis ?: ($astap->jenisAstap?->jenis ?: 'PELIMPAHAN SKPD'),
                'tahun_perolehan'         => $tahunMasuk,
                'volume_satuan'           => $volAset . ' Aset',
                'jumlah_volume'           => $volAset,
                'satuan'                  => $astap->satuan ?: 'Unit',
                'harga_satuan'            => (float) ($astap->harga_satuan ?: ($volAset > 0 ? ($nilaiReal / $volAset) : $nilaiReal)),
                'jumlah_realisasi'        => 'Rp ' . number_format($nilaiReal, 0, ',', '.'),
                'total_realisasi_num'     => $nilaiReal,
                'item_count'              => $itemCount,
                'items'                   => $items,
                'registers'               => $astap->registers ? $astap->registers->toArray() : [],
                'kode_barang'             => $kode108 ?: $firstNibar,
                'kode_108'                => $kode108,
                'kondisi'                 => $items[0]['kondisi'] ?? 'Baik',
                'opd_asal'                => $opdAsal,
                'ruangan_asal'            => $opdAsal,
                'pj_asal_nama'            => 'Pejabat Penyerah SKPD Pengirim',
                'pj_asal_nip'             => '-',
                'pj_asal_jabatan'         => 'Pengurus Barang / PPK Asal',
                'opd_tujuan'              => $opdTujuan,
                'ruangan_tujuan'          => $ruangRSUD,
                'pejabat_opd_tujuan'      => $pjNama,
                'nip_pejabat_opd_tujuan'  => $pjNip,
                'jabatan_opd_tujuan'      => 'Pengurus Barang / PPK RSUD Dr. H. Koesnadi',
                'nomor_sk_dasar'          => $nomorBamb,
                'tgl'                     => $tglFormatted,
                'tgl_raw'                 => (string) $tglRaw,
                'status'                  => 'Disahkan (Selesai)',
                'alasan_mutasi'           => $astap->mutasi_keterangan 
                                              ?: ($astap->pelimpahanSkpd?->keterangan 
                                              ?: 'Pelimpahan aset barang milik daerah dari SKPD/Dinas luar ke RSUD dr. H. Koesnadi.'),
                'tgl_estimasi_kembali'    => null,
                'dokumen_lampiran'        => null,
                'nilai_perolehan'         => $nilaiReal,
            ];
        })->values()->toArray();

        return view('pages.mutasi_eksternal', compact('mutasiEksternals'));
    }

    /**
     * Tampilkan form pengajuan mutasi baru.
     */
    public function create()
    {
        $lockedIds = self::getLockedRegisterIds();

        $units = Unit::orderBy('nama')->get(['id', 'nama', 'kepala']);
        $rawRegisters = AstapRegister::with('astap', 'unit')
            ->whereNotNull('nibar')
            ->whereNotIn('id', $lockedIds)
            ->orderBy('id')
            ->get();

        $registers = $rawRegisters->map(function ($r) {
            return [
                'id'          => $r->id,
                'nibar'       => $r->nibar ?? '-',
                'nama_barang' => $r->astap?->nama_barang ?? '-',
                'kondisi'     => $r->kondisi ?? 'Baik',
                'unit_nama'   => $r->unit?->nama ?? ($r->ruang_pemegang ?? '-'),
                'unit_kepala' => $r->unit?->kepala ?? '-',
            ];
        });

        // Hitung jumlah aset yang sedang terkunci per unit agar bisa ditampilkan banner informatif jika ada
        $lockedRegisters = AstapRegister::with('unit')
            ->whereIn('id', $lockedIds)
            ->get(['id', 'unit_id', 'ruang_pemegang']);
        $lockedCountByUnit = [];
        foreach ($lockedRegisters as $lr) {
            $uName = $lr->unit?->nama ?? ($lr->ruang_pemegang ?? '');
            if ($uName) {
                $norm = strtolower(trim($uName));
                $lockedCountByUnit[$norm] = ($lockedCountByUnit[$norm] ?? 0) + 1;
            }
        }

        return view('pages.form_mutasi_aset', compact('units', 'registers', 'lockedCountByUnit'));
    }

    /**
     * Simpan pengajuan mutasi ke database.
     * Menggunakan pola Header-Detail (1 Berita Acara BAMB memuat N barang).
     */
    public function store(Request $request)
    {
        $request->validate([
            'astap_register_id'       => 'nullable|exists:astap_registers,id',
            'astap_register_ids'      => 'nullable|array',
            'astap_register_ids.*'    => 'exists:astap_registers,id',
            'jenis_mutasi'            => 'required|in:Ajukan Mutasi,Pemindahan,Perbaikan,Minta Mutasi,Pengembalian,Penghapusan',
            'tanggal_mutasi'          => 'required|date',
            'ruangan_asal'            => 'required|string|max:255',
            'ruangan_tujuan'          => 'required|string|max:255|different:ruangan_asal',
            'penanggung_jawab_asal'   => 'required|string|max:255',
            'penanggung_jawab_tujuan' => 'required|string|max:255',
            'alasan_mutasi'           => 'required|string|max:2000',
            'catatan_penerima'        => 'nullable|string|max:1000',
        ], [
            'jenis_mutasi.required'            => 'Jenis mutasi wajib dipilih pada Tahap 1.',
            'jenis_mutasi.in'                  => 'Jenis mutasi yang dipilih tidak valid.',
            'tanggal_mutasi.required'          => 'Tanggal mutasi wajib diisi.',
            'ruangan_asal.required'            => 'Unit / Ruangan asal pengirim wajib ditentukan.',
            'ruangan_tujuan.required'          => 'Unit / Ruangan tujuan penerima wajib ditentukan.',
            'ruangan_tujuan.different'         => 'Ruangan tujuan penerima harus berbeda dengan ruangan asal pengirim.',
            'penanggung_jawab_asal.required'   => 'Penanggung jawab unit asal wajib diisi.',
            'penanggung_jawab_tujuan.required' => 'Penanggung jawab unit tujuan wajib diisi.',
            'alasan_mutasi.required'           => 'Alasan / keperluan mutasi wajib diisi.',
        ]);

        $registerIds = $request->input('astap_register_ids', []);
        if (empty($registerIds) && $request->astap_register_id) {
            $registerIds = [$request->astap_register_id];
        }

        if (empty($registerIds)) {
            return back()->withErrors(['astap_register_id' => 'Silakan pilih minimal 1 barang aset yang akan dimutasi.']);
        }

        // Pastikan tidak ada barang yang sedang terkunci dalam proses mutasi aktif lain
        $lockedIds = self::getLockedRegisterIds();
        $conflictIds = array_intersect(array_map('intval', $registerIds), $lockedIds);
        if (!empty($conflictIds)) {
            $conflictNames = AstapRegister::whereIn('id', $conflictIds)->with('astap')->get()->map(function ($r) {
                return ($r->astap?->nama_barang ?? 'Aset') . " (" . ($r->nibar ?? '-') . ")";
            })->implode(', ');
            return back()->withInput()->withErrors([
                'astap_register_id' => "Barang aset berikut sedang dalam proses pengajuan mutasi lain dan belum selesai: {$conflictNames}."
            ]);
        }

        $kondisiBaru = $request->input('kondisi_baru', []);

        // 1. Generate 1 nomor Berita Acara BAMB unik (format 3 digit: MTS-2026-001)
        $year  = date('Y', strtotime($request->tanggal_mutasi));
        $count = AstapMutasi::whereYear('tanggal_mutasi', $year)->count();
        $seq   = $count + 1;
        do {
            $nomor = 'MTS-' . $year . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
            $exists = AstapMutasi::where('nomor_bamb', $nomor)->exists();
            if ($exists) {
                $seq++;
            }
        } while ($exists);

        $firstRegId = $registerIds[0] ?? null;

        $user = Auth::user();
        $userRole = $user->role ?? 'admin';
        $isAdminRole = in_array($userRole, ['admin', 'master_admin']);

        $isReturnToGudang = ($request->jenis_mutasi === 'Pengembalian') && (
            str_contains(strtolower($request->ruangan_tujuan), 'perbekalan') ||
            str_contains(strtolower($request->ruangan_tujuan), 'rumah tangga') ||
            str_contains(strtolower($request->ruangan_tujuan), 'gudang')
        );

        if ($isAdminRole) {
            // Master Admin & Admin: Otomatis persetujuan Admin disetujui.
            // - Pengembalian ke Gudang: Hanya butuh persetujuan Pengirim
            // - Mutasi Biasa: Butuh persetujuan Pengirim dan Penerima
            $pAdmin    = true;
            $tglAdmin  = now();
            $pPenerima = $isReturnToGudang ? true : false;
            $tglPen    = $isReturnToGudang ? now() : null;
            $pPengirim = false;
            $tglPeng   = null;

            $initialStatus = $isReturnToGudang
                ? 'Disetujui Admin (Menunggu Persetujuan Pengirim)'
                : 'Disetujui Admin (Menunggu Persetujuan Pengirim & Penerima)';
        } else {
            // Sub Admin: Otomatis persetujuan Pengirim disetujui (kecuali Minta Mutasi di mana Sub Admin = Penerima)
            // - Pengembalian ke Gudang: Hanya butuh persetujuan Admin
            // - Mutasi Biasa: Butuh persetujuan Penerima dan Admin
            $pAdmin   = false;
            $tglAdmin = null;

            if ($request->jenis_mutasi === 'Minta Mutasi') {
                $pPengirim = false;
                $tglPeng   = null;
                $pPenerima = true;
                $tglPen    = now();
                $initialStatus = 'Menunggu Persetujuan Pengirim';
            } else {
                $pPengirim = true;
                $tglPeng   = now();
                $pPenerima = $isReturnToGudang ? true : false;
                $tglPen    = $isReturnToGudang ? now() : null;

                $initialStatus = $isReturnToGudang
                    ? 'Menunggu Persetujuan Admin'
                    : 'Menunggu Persetujuan Penerima';
            }
        }

        // 2. Buat 1 baris Dokumen Berita Acara (Header)
        $mutasi = AstapMutasi::create([
            'astap_register_id'        => $firstRegId,
            'nomor_bamb'               => $nomor,
            'tanggal_mutasi'           => $request->tanggal_mutasi,
            'jenis_mutasi'             => $request->jenis_mutasi,
            'ruangan_asal'             => $request->ruangan_asal,
            'ruangan_tujuan'           => $request->ruangan_tujuan,
            'penanggung_jawab_asal'    => $request->penanggung_jawab_asal,
            'penanggung_jawab_tujuan'  => $request->penanggung_jawab_tujuan,
            'alasan_mutasi'            => $request->alasan_mutasi,
            'catatan_penerima'         => $request->catatan_penerima,
            'persetujuan_pengirim'     => $pPengirim,
            'tgl_persetujuan_pengirim' => $tglPeng,
            'persetujuan_penerima'     => $pPenerima,
            'tgl_persetujuan_penerima' => $tglPen,
            'persetujuan_admin'        => $pAdmin,
            'tgl_persetujuan_admin'    => $tglAdmin,
            'status'                   => $initialStatus,
            'is_deleted'               => 0,
            'deleted_by'               => null,
            'deleted_by_id'            => null,
            'deleted_at'               => null,
        ]);

        // 3. Masukkan seluruh item register yang dimutasi ke tabel rincian astap_mutasi_registers
        $createdCount = 0;
        foreach ($registerIds as $regId) {
            $regObj = AstapRegister::find($regId);
            if (isset($kondisiBaru[$regId]) && in_array($kondisiBaru[$regId], ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'])) {
                if ($regObj && $regObj->kondisi !== $kondisiBaru[$regId]) {
                    $regObj->update(['kondisi' => $kondisiBaru[$regId]]);
                }
            }
            $kondisiSaatMutasi = $regObj ? $regObj->kondisi : 'Baik';

            AstapMutasiRegister::create([
                'astap_mutasi_id'   => $mutasi->id,
                'astap_register_id' => $regId,
                'kondisi'           => $kondisiSaatMutasi,
            ]);
            $createdCount++;
        }

        // Kirim Notifikasi Sistem (Format Singkat & Rapi)
        try {
            \App\Services\NotificationService::sendToAdminAndMaster(
                "Mutasi: {$mutasi->ruangan_asal} → {$mutasi->ruangan_tujuan}",
                "{$mutasi->nomor_bamb} • Menunggu Penerima",
                'mutasi',
                route('mutasi.index')
            );
            $targetUnit = Unit::where('nama', $mutasi->ruangan_tujuan)->first();
            \App\Services\NotificationService::sendToUnitSubAdmin(
                $targetUnit?->id,
                $mutasi->ruangan_tujuan,
                "Mutasi Masuk: dari {$mutasi->ruangan_asal}",
                "{$mutasi->nomor_bamb} • Perlu Konfirmasi",
                'mutasi',
                route('mutasi.index')
            );
        } catch (\Throwable $e) {
            \Log::warning("Gagal kirim notif mutasi store: " . $e->getMessage());
        }

        $msg = $createdCount > 1
            ? "Pengajuan mutasi {$request->jenis_mutasi} sebanyak {$createdCount} barang aset berhasil dibuat dalam 1 Berita Acara ({$nomor})! Menunggu persetujuan penerima."
            : "Pengajuan mutasi {$request->jenis_mutasi} ({$nomor}) berhasil dikirim! Menunggu persetujuan penerima.";

        return redirect()->route('mutasi.index')->with('success', $msg);
    }

    /**
     * Tampilkan form edit mutasi.
     */
    public function edit($id)
    {
        $mutasi = AstapMutasi::with(['items.register.astap', 'items.register.unit', 'register.astap'])->findOrFail($id);

        if ($mutasi->is_deleted) {
            return redirect()->route('mutasi.index')->with('error', 'Pengajuan mutasi ini telah dihapus dan tidak dapat diedit.');
        }

        if ($mutasi->status === 'Ditolak') {
            return redirect()->route('mutasi.index')->with('error', 'Pengajuan mutasi ini berstatus Ditolak dan terkunci. Silakan batalkan penolakan terlebih dahulu melalui menu Detail.');
        }

        $units  = Unit::orderBy('nama')->get(['id', 'nama', 'kepala']);

        $relatedRegisterIds = $mutasi->items->pluck('astap_register_id')->filter()->unique()->values()->toArray();
        if (empty($relatedRegisterIds) && $mutasi->astap_register_id) {
            $relatedRegisterIds = [$mutasi->astap_register_id];
        }

        $lockedIds = self::getLockedRegisterIds($id);

        $rawRegisters = AstapRegister::with('astap', 'unit')
            ->whereNotNull('nibar')
            ->whereNotIn('id', $lockedIds)
            ->orderBy('id')
            ->get();
        $registers = $rawRegisters->map(function ($r) {
            return [
                'id'          => $r->id,
                'nibar'       => $r->nibar ?? '-',
                'nama_barang' => $r->astap?->nama_barang ?? '-',
                'kondisi'     => $r->kondisi ?? 'Baik',
                'unit_nama'   => $r->unit?->nama ?? ($r->ruang_pemegang ?? '-'),
                'unit_kepala' => $r->unit?->kepala ?? '-',
            ];
        });

        $lockedRegisters = AstapRegister::with('unit')
            ->whereIn('id', $lockedIds)
            ->get(['id', 'unit_id', 'ruang_pemegang']);
        $lockedCountByUnit = [];
        foreach ($lockedRegisters as $lr) {
            $uName = $lr->unit?->nama ?? ($lr->ruang_pemegang ?? '');
            if ($uName) {
                $norm = strtolower(trim($uName));
                $lockedCountByUnit[$norm] = ($lockedCountByUnit[$norm] ?? 0) + 1;
            }
        }

        return view('pages.form_mutasi_aset', compact('mutasi', 'units', 'registers', 'relatedRegisterIds', 'lockedCountByUnit'));
    }

    /**
     * Update data mutasi.
     */
    public function update(Request $request, $id)
    {
        $mutasi = AstapMutasi::findOrFail($id);

        if ($mutasi->is_deleted) {
            return redirect()->route('mutasi.index')->with('error', 'Pengajuan mutasi ini telah dihapus dan tidak dapat diubah.');
        }

        $request->validate([
            'astap_register_ids'      => 'nullable|array',
            'astap_register_ids.*'    => 'exists:astap_registers,id',
            'astap_register_id'       => 'nullable|exists:astap_registers,id',
            'jenis_mutasi'            => 'required|in:Ajukan Mutasi,Pemindahan,Perbaikan,Minta Mutasi,Pengembalian,Penghapusan',
            'tanggal_mutasi'          => 'nullable|date',
            'ruangan_asal'            => 'required|string|max:255',
            'ruangan_tujuan'          => 'required|string|max:255|different:ruangan_asal',
            'penanggung_jawab_asal'   => 'required|string|max:255',
            'penanggung_jawab_tujuan' => 'required|string|max:255',
            'alasan_mutasi'           => 'required|string|max:2000',
            'catatan_penerima'        => 'nullable|string|max:1000',
        ], [
            'jenis_mutasi.required'            => 'Jenis mutasi wajib dipilih pada Tahap 1.',
            'jenis_mutasi.in'                  => 'Jenis mutasi yang dipilih tidak valid.',
            'ruangan_asal.required'            => 'Unit / Ruangan asal pengirim wajib ditentukan.',
            'ruangan_tujuan.required'          => 'Unit / Ruangan tujuan penerima wajib ditentukan.',
            'ruangan_tujuan.different'         => 'Ruangan tujuan penerima harus berbeda dengan ruangan asal pengirim.',
            'penanggung_jawab_asal.required'   => 'Penanggung jawab unit asal wajib diisi.',
            'penanggung_jawab_tujuan.required' => 'Penanggung jawab unit tujuan wajib diisi.',
            'alasan_mutasi.required'           => 'Alasan / keperluan mutasi wajib diisi.',
        ]);

        $registerIds = $request->input('astap_register_ids', []);
        if (empty($registerIds) && $request->astap_register_id) {
            $registerIds = [$request->astap_register_id];
        }
        if (empty($registerIds)) {
            $registerIds = [$mutasi->astap_register_id];
        }

        // Pastikan tidak ada barang yang bentrok dengan mutasi aktif lain
        $lockedIds = self::getLockedRegisterIds($id);
        $conflictIds = array_intersect(array_map('intval', $registerIds), $lockedIds);
        if (!empty($conflictIds)) {
            $conflictNames = AstapRegister::whereIn('id', $conflictIds)->with('astap')->get()->map(function ($r) {
                return ($r->astap?->nama_barang ?? 'Aset') . " (" . ($r->nibar ?? '-') . ")";
            })->implode(', ');
            return back()->withInput()->withErrors([
                'astap_register_id' => "Barang berikut sedang dalam proses pengajuan mutasi lain: {$conflictNames}."
            ]);
        }

        $kondisiBaru = $request->input('kondisi_baru', []);

        $newTgl  = $request->tanggal_mutasi ?: ($mutasi->tanggal_mutasi ?: now());
        $newYear = date('Y', strtotime($newTgl));
        
        // Sinkronkan nomor_bamb agar mengikuti tahun mutasi dan format 3 digit (MTS-YYYY-XXX)
        preg_match('/^MTS-(\d{4})-(\d+)$/', $mutasi->nomor_bamb, $matches);
        $oldYear   = $matches[1] ?? null;
        $seqLength = strlen($matches[2] ?? '');

        if ($oldYear !== $newYear || $seqLength !== 3) {
            $seq = AstapMutasi::whereYear('tanggal_mutasi', $newYear)->where('id', '<=', $mutasi->id)->count();
            if ($seq < 1) $seq = 1;
            $newNomorBamb = 'MTS-' . $newYear . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
            $mutasi->nomor_bamb = $newNomorBamb;
        }

        // 1. Update Dokumen Header Mutasi
        $firstRegId = $registerIds[0] ?? $mutasi->astap_register_id;
        $mutasi->update([
            'astap_register_id'        => $firstRegId,
            'nomor_bamb'               => $mutasi->nomor_bamb,
            'tanggal_mutasi'           => $newTgl,
            'jenis_mutasi'             => $request->jenis_mutasi,
            'ruangan_asal'             => $request->ruangan_asal,
            'ruangan_tujuan'           => $request->ruangan_tujuan,
            'penanggung_jawab_asal'    => $request->penanggung_jawab_asal,
            'penanggung_jawab_tujuan'  => $request->penanggung_jawab_tujuan,
            'alasan_mutasi'            => $request->alasan_mutasi,
            'catatan_penerima'         => $request->catatan_penerima,
        ]);

        // 2. Sinkronkan Rincian Item Register
        AstapMutasiRegister::where('astap_mutasi_id', $mutasi->id)
            ->whereNotIn('astap_register_id', $registerIds)
            ->delete();

        foreach ($registerIds as $regId) {
            $regObj = AstapRegister::find($regId);
            if (isset($kondisiBaru[$regId]) && in_array($kondisiBaru[$regId], ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'])) {
                if ($regObj && $regObj->kondisi !== $kondisiBaru[$regId]) {
                    $regObj->update(['kondisi' => $kondisiBaru[$regId]]);
                }
            }
            $kondisiSaatMutasi = $regObj ? $regObj->kondisi : 'Baik';

            AstapMutasiRegister::updateOrCreate(
                [
                    'astap_mutasi_id'   => $mutasi->id,
                    'astap_register_id' => $regId,
                ],
                [
                    'kondisi'           => $kondisiSaatMutasi,
                ]
            );
        }

        return redirect()->route('mutasi.index')
            ->with('success', 'Pengajuan Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') berhasil diperbarui.');
    }

    /**
     * Hapus pengajuan mutasi (Soft Delete: mengubah nilai is_deleted dari 0 menjadi 1, catat user & waktu hapus).
     */
    public function destroy(Request $request, $id)
    {
        $mutasi = AstapMutasi::findOrFail($id);
        $bamb   = $mutasi->nomor_bamb;
        $user   = Auth::user();
        $deleterName = $user ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') : 'Administrator';

        // Ubah nilai label status hapus dari 0 menjadi 1, catat siapa yang menghapus dan tanggal & jam dihapus
        $mutasi->update([
            'is_deleted'    => 1,
            'deleted_by'    => $deleterName,
            'deleted_by_id' => $user?->id,
            'deleted_at'    => now(),
        ]);

        $msg = "Data Berita Acara Mutasi {$bamb} berhasil dihapus (label status diubah menjadi 1).";
        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => $msg,
                'deleted_by' => $deleterName,
                'deleted_at' => now()->translatedFormat('d M Y, H:i') . ' WIB',
            ]);
        }

        return redirect()->route('mutasi.index')->with('success', $msg);
    }

    /**
     * Pulihkan pengajuan mutasi yang pernah dihapus (mengubah label is_deleted dari 1 kembali ke 0).
     */
    public function restore(Request $request, $id)
    {
        $mutasi = AstapMutasi::findOrFail($id);
        $bamb   = $mutasi->nomor_bamb;

        $mutasi->update([
            'is_deleted'    => 0,
            'deleted_by'    => null,
            'deleted_by_id' => null,
            'deleted_at'    => null,
        ]);

        $msg = "Data Berita Acara Mutasi {$bamb} berhasil dipulihkan (label status dikembalikan menjadi 0).";
        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('mutasi.index')->with('success', $msg);
    }

    /**
     * Helper privat untuk memindahkan lokasi register aset setelah mutasi disetujui penuh
     */
    private function transferRegisterLocations(AstapMutasi $mutasi): void
    {
        $targetUnit = Unit::where('nama', $mutasi->ruangan_tujuan)
            ->orWhereRaw('LOWER(TRIM(nama)) = ?', [strtolower(trim($mutasi->ruangan_tujuan))])
            ->first();

        $isReturnToGudang = ($mutasi->jenis_mutasi === 'Pengembalian') && (
            !$targetUnit ||
            str_contains(strtolower($mutasi->ruangan_tujuan), 'perbekalan') ||
            str_contains(strtolower($mutasi->ruangan_tujuan), 'rumah tangga') ||
            str_contains(strtolower($mutasi->ruangan_tujuan), 'gudang')
        );

        $registersToUpdate = [];
        if ($mutasi->items->count() > 0) {
            foreach ($mutasi->items as $item) {
                if ($item->register) {
                    $registersToUpdate[] = [
                        'register' => $item->register,
                        'kondisi'  => $item->kondisi,
                    ];
                }
            }
        } elseif ($mutasi->register) {
            $registersToUpdate[] = [
                'register' => $mutasi->register,
                'kondisi'  => $mutasi->register->kondisi ?? 'Baik',
            ];
        }

        foreach ($registersToUpdate as $pair) {
            $reg = $pair['register'];
            $kondisi = $pair['kondisi'];

            if ($isReturnToGudang) {
                $updateData = [
                    'unit_id'        => null,
                    'ruang_pemegang' => null,
                    'status'         => 'Tersedia',
                ];
            } else {
                $updateData = [
                    'ruang_pemegang' => $mutasi->ruangan_tujuan,
                    'unit_id'        => $targetUnit?->id ?? $reg->unit_id,
                ];
            }
            if ($kondisi) {
                $updateData['kondisi'] = $kondisi;
            }
            if ($mutasi->jenis_mutasi === 'Penghapusan') {
                $updateData['status'] = 'Dihapuskan';
            }
            $reg->update($updateData);
        }
    }

    /**
     * Persetujuan oleh pihak pengirim (Sub Admin ruangan asal).
     */
    public function approvePengirim(Request $request, $id)
    {
        $mutasi = AstapMutasi::with(['items.register', 'register'])->findOrFail($id);
        if ($mutasi->is_deleted) {
            return back()->with('error', 'Pengajuan mutasi ini telah dihapus.');
        }

        $isReturnToGudang = ($mutasi->jenis_mutasi === 'Pengembalian') && (
            str_contains(strtolower($mutasi->ruangan_tujuan), 'perbekalan') ||
            str_contains(strtolower($mutasi->ruangan_tujuan), 'rumah tangga') ||
            str_contains(strtolower($mutasi->ruangan_tujuan), 'gudang')
        );

        $isCompleted = ($isReturnToGudang && $mutasi->persetujuan_admin) ||
            ($mutasi->persetujuan_admin && $mutasi->persetujuan_penerima);

        $newStatus = $isCompleted ? 'Disetujui Admin (Selesai)' : 'Disetujui Pengirim (Menunggu Pihak Lain)';

        $mutasi->update([
            'persetujuan_pengirim'     => true,
            'tgl_persetujuan_pengirim' => now(),
            'status'                   => $newStatus,
        ]);

        if ($isCompleted) {
            $this->transferRegisterLocations($mutasi);
        }

        $msg = 'Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') berhasil disetujui oleh pengirim.';
        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_completed' => $isCompleted]);
        }
        return back()->with('success', $msg);
    }

    /**
     * Persetujuan oleh pihak penerima (Sub Admin ruangan tujuan).
     */
    public function approvePenerima(Request $request, $id)
    {
        $mutasi = AstapMutasi::with(['items.register', 'register'])->findOrFail($id);
        if ($mutasi->is_deleted) {
            return back()->with('error', 'Pengajuan mutasi ini telah dihapus.');
        }

        $isCompleted = (bool) $mutasi->persetujuan_admin && (bool) $mutasi->persetujuan_pengirim;
        $newStatus   = $isCompleted ? 'Disetujui Admin (Selesai)' : 'Disetujui Penerima (Menunggu Pihak Lain)';

        $mutasi->update([
            'persetujuan_penerima'     => true,
            'tgl_persetujuan_penerima' => now(),
            'status'                   => $newStatus,
        ]);

        if ($isCompleted) {
            $this->transferRegisterLocations($mutasi);
        }

        // Kirim Notifikasi Sistem (Format Singkat & Rapi)
        try {
            \App\Services\NotificationService::sendToAdminAndMaster(
                "Mutasi Disetujui Penerima",
                "{$mutasi->nomor_bamb} • " . ($isCompleted ? 'Selesai' : 'Menunggu Pihak Lain'),
                'mutasi',
                route('mutasi.index')
            );
            $asalUnit = Unit::where('nama', $mutasi->ruangan_asal)->first();
            \App\Services\NotificationService::sendToUnitSubAdmin(
                $asalUnit?->id,
                $mutasi->ruangan_asal,
                "Mutasi Disetujui Penerima: {$mutasi->ruangan_tujuan}",
                "{$mutasi->nomor_bamb} • Status: " . ($isCompleted ? 'Selesai' : 'Menunggu Pihak Lain'),
                'mutasi',
                route('mutasi.index')
            );
        } catch (\Throwable $e) {
            \Log::warning("Gagal kirim notif approvePenerima: " . $e->getMessage());
        }

        $msg = 'Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') berhasil disetujui oleh penerima.';
        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_completed' => $isCompleted]);
        }
        return back()->with('success', $msg);
    }

    /**
     * Persetujuan oleh Admin / Master Admin.
     */
    public function approveAdmin(Request $request, $id)
    {
        $mutasi = AstapMutasi::with(['items.register', 'register'])->findOrFail($id);
        if ($mutasi->is_deleted) {
            return back()->with('error', 'Pengajuan mutasi ini telah dihapus.');
        }

        $isReturnToGudang = ($mutasi->jenis_mutasi === 'Pengembalian') && (
            str_contains(strtolower($mutasi->ruangan_tujuan), 'perbekalan') ||
            str_contains(strtolower($mutasi->ruangan_tujuan), 'rumah tangga') ||
            str_contains(strtolower($mutasi->ruangan_tujuan), 'gudang')
        );

        $isCompleted = ($isReturnToGudang && $mutasi->persetujuan_pengirim) ||
            ($mutasi->persetujuan_pengirim && $mutasi->persetujuan_penerima);

        $newStatus = $isCompleted ? 'Disetujui Admin (Selesai)' : 'Disetujui Admin (Menunggu Persetujuan Ruangan)';

        $updateData = [
            'persetujuan_admin'     => true,
            'tgl_persetujuan_admin' => now(),
            'status'                => $newStatus,
        ];
        if ($isReturnToGudang) {
            $updateData['persetujuan_penerima'] = true;
            $updateData['tgl_persetujuan_penerima'] = now();
        }

        $mutasi->update($updateData);

        if ($isCompleted) {
            $this->transferRegisterLocations($mutasi);
        }

        // Kirim Notifikasi Sistem ke Sub Admin Ruangan Asal dan Ruangan Tujuan
        try {
            $asalUnit   = Unit::where('nama', $mutasi->ruangan_asal)->first();
            $targetUnit = Unit::where('nama', $mutasi->ruangan_tujuan)->first();
            \App\Services\NotificationService::sendToUnitSubAdmin(
                $asalUnit?->id,
                $mutasi->ruangan_asal,
                "Mutasi Disetujui Admin: {$mutasi->ruangan_tujuan}",
                "{$mutasi->nomor_bamb} • Status: " . ($isCompleted ? 'Selesai' : 'Menunggu Penerima'),
                'mutasi',
                route('mutasi.index')
            );
            \App\Services\NotificationService::sendToUnitSubAdmin(
                $targetUnit?->id,
                $mutasi->ruangan_tujuan,
                "Mutasi Disetujui Admin: dari {$mutasi->ruangan_asal}",
                "{$mutasi->nomor_bamb} • Status: " . ($isCompleted ? 'Selesai' : 'Menunggu Penerima'),
                'mutasi',
                route('mutasi.index')
            );
        } catch (\Throwable $e) {
            \Log::warning("Gagal kirim notif approveAdmin: " . $e->getMessage());
        }

        $msg = $isCompleted
            ? "Berita Acara Mutasi ({$mutasi->nomor_bamb}) telah disahkan secara final oleh Admin! Lokasi aset dipindahkan."
            : "Berita Acara Mutasi ({$mutasi->nomor_bamb}) berhasil disetujui oleh Admin. Menunggu persetujuan penerima untuk penyelesaian final.";

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_completed' => $isCompleted]);
        }
        return back()->with('success', $msg);
    }

    /**
     * Tolak pengajuan mutasi.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        $mutasi = AstapMutasi::findOrFail($id);
        if ($mutasi->is_deleted) {
            return back()->with('error', 'Pengajuan mutasi ini telah dihapus.');
        }

        $alasan = $request->input('alasan_penolakan') ?? $request->json('alasan_penolakan');

        $mutasi->update([
            'status'           => 'Ditolak',
            'alasan_penolakan' => $alasan,
        ]);

        // Kirim Notifikasi Sistem ke Sub Admin Ruangan Asal (Format Singkat & Rapi)
        try {
            $asalUnit = Unit::where('nama', $mutasi->ruangan_asal)->first();
            \App\Services\NotificationService::sendToUnitSubAdmin(
                $asalUnit?->id,
                $mutasi->ruangan_asal,
                "Mutasi Ditolak: {$mutasi->ruangan_tujuan}",
                "{$mutasi->nomor_bamb} • {$alasan}",
                'mutasi',
                route('mutasi.index')
            );
        } catch (\Throwable $e) {
            \Log::warning("Gagal kirim notif reject: " . $e->getMessage());
        }

        session()->flash('success', 'Pengajuan Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') ditolak.');
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Pengajuan Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') ditolak.');
    }

    /**
     * Batalkan status penolakan mutasi (mengembalikan mutasi ke status aktif dan mereset batas 24 jam).
     */
    public function cancelReject(Request $request, $id)
    {
        $mutasi = AstapMutasi::with(['items.register', 'register'])->findOrFail($id);
        if ($mutasi->is_deleted) {
            return back()->with('error', 'Pengajuan mutasi ini telah dihapus.');
        }

        if ($mutasi->status !== 'Ditolak') {
            return back()->with('info', 'Mutasi ini tidak dalam status ditolak.');
        }

        $user = Auth::user();
        $userRole = $user->role ?? 'admin';
        $isAdmin = in_array($userRole, ['admin', 'master_admin']);

        // Jika sub admin, pastikan terlibat di ruangan asal atau tujuan
        if (!$isAdmin) {
            $userUnit = strtolower(trim($user->unitModel?->nama ?? ($user->unit ?? '')));
            $asal = strtolower(trim($mutasi->ruangan_asal));
            $tujuan = strtolower(trim($mutasi->ruangan_tujuan));
            if ($userUnit && !str_contains($asal, $userUnit) && !str_contains($userUnit, $asal) && !str_contains($tujuan, $userUnit) && !str_contains($userUnit, $tujuan)) {
                $msg = 'Anda tidak memiliki hak akses untuk membatalkan penolakan mutasi ini.';
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }
                return back()->with('error', $msg);
            }
        }

        // Tentukan status kembalian yang tepat
        $isReturnToGudang = ($mutasi->jenis_mutasi === 'Pengembalian') && (
            str_contains(strtolower($mutasi->ruangan_tujuan), 'perbekalan') ||
            str_contains(strtolower($mutasi->ruangan_tujuan), 'rumah tangga') ||
            str_contains(strtolower($mutasi->ruangan_tujuan), 'gudang')
        );

        if ($mutasi->persetujuan_admin && !$mutasi->persetujuan_penerima) {
            $newStatus = $isReturnToGudang
                ? 'Disetujui Admin (Menunggu Persetujuan Pengirim)'
                : 'Disetujui Admin (Menunggu Persetujuan Pengirim & Penerima)';
        } elseif ($mutasi->persetujuan_pengirim && !$mutasi->persetujuan_penerima) {
            $newStatus = $isReturnToGudang ? 'Menunggu Persetujuan Admin' : 'Menunggu Persetujuan Penerima';
        } elseif ($mutasi->persetujuan_penerima && !$mutasi->persetujuan_admin) {
            $newStatus = 'Disetujui 2 Pihak (Menunggu Admin)';
        } else {
            $newStatus = 'Menunggu Persetujuan Penerima';
        }

        $mutasi->update([
            'status'           => $newStatus,
            'alasan_penolakan' => null,
        ]);

        $msg = "Penolakan mutasi {$mutasi->nomor_bamb} berhasil dibatalkan. Status dikembalikan ke '{$newStatus}'.";
        session()->flash('success', $msg);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'new_status' => $newStatus]);
        }

        return back()->with('success', $msg);
    }

    /**
     * API: Ambil data barang berdasarkan astap_register_id untuk autocomplete form.
     */
    public function getRegisterData($id)
    {
        $register = AstapRegister::with('astap', 'unit')->find($id);

        if (!$register) {
            return response()->json(['success' => false], 404);
        }

        return response()->json([
            'success'        => true,
            'id'             => $register->id,
            'nibar'          => $register->nibar,
            'nama_barang'    => $register->astap->nama_barang ?? '-',
            'kondisi'        => $register->kondisi,
            'ruang_pemegang' => $register->ruang_pemegang,
            'unit_id'        => $register->unit_id,
            'unit_nama'      => $register->unit->nama ?? '-',
            'unit_kepala'    => $register->unit->kepala ?? '-',
        ]);
    }
}

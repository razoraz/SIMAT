<?php

namespace App\Http\Controllers;

use App\Models\AstapMutasi;
use App\Models\AstapMutasiRegister;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapHibah;
use App\Models\AstapKemitraan;
use App\Models\Distribusi;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecycleBinController extends Controller
{
    /**
     * Tampilkan halaman utama Pusat Data Terhapus (Recycle Bin Center)
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $activeTab = $request->query('tab', 'astap');
        if ($activeTab === 'bast') {
            $activeTab = 'astap';
        }

        // =========================================================================
        // 1. DATA TERHAPUS: MUTASI ASET
        // =========================================================================
        $rawDeletedMutasis = AstapMutasi::onlyDeleted()
            ->with(['items.register.astap', 'items.register.unit', 'register.astap'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedMutasis = $rawDeletedMutasis->map(function ($m) {
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
                ];
            })->values()->toArray();

            $deletedAt = $m->deleted_at ? Carbon::parse($m->deleted_at)->timezone('Asia/Jakarta') : null;

            return [
                'id'                   => $m->id,
                'kode'                 => $m->nomor_bamb,
                'jenis'                => $m->jenis_mutasi,
                'nama'                 => $itemSummary,
                'kode_barang'          => $firstRegister?->nibar ?? '-',
                'item_count'           => $itemCount,
                'items'                => $itemsMapped,
                'asal'                 => $m->ruangan_asal,
                'tujuan'               => $m->ruangan_tujuan,
                'pemohon'              => $m->penanggung_jawab_asal,
                'penerima_pj'          => $m->penanggung_jawab_tujuan,
                'status_terakhir'      => $m->status,
                'alasan_mutasi'        => $m->alasan_mutasi ?: '-',
                'deleted_by'           => $m->deleted_by ?: 'Administrator',
                'deleted_at'           => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative'  => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'       => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 1B. DATA TERHAPUS: MUTASI EKSTERNAL (TRANSFER ANTAR-OPD / PELIMPAHAN SKPD)
        // =========================================================================
        $rawDeletedMutasiEksternals = \App\Models\MutasiEksternal::onlyDeleted()
            ->with(['astap.registers', 'astap.jenisAstap', 'unit', 'user'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedMutasiEksternals = $rawDeletedMutasiEksternals->map(function ($m) {
            $astap = $m->astap;
            $nomorBamb = $m->nomor_bamb ?: ($astap?->bast_dokumen_nomor ?: 'BAMB-EXT-' . str_pad($m->id, 4, '0', STR_PAD_LEFT));
            $kode108 = $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->jenis ?: '-'));

            $itemsMapped = [];
            $registers = $astap?->registers ?? collect();
            if ($registers->isNotEmpty()) {
                foreach ($registers as $idx => $reg) {
                    $itemsMapped[] = [
                        'no'          => $idx + 1,
                        'nama_barang' => $astap->nama_barang,
                        'nibar'       => $reg->nibar ?: '-',
                        'kode_108'    => $kode108,
                        'kondisi'     => $reg->kondisi ?: ($m->kondisi ?: 'Baik'),
                    ];
                }
            } else {
                $itemsMapped[] = [
                    'no'          => 1,
                    'nama_barang' => $astap?->nama_barang ?: 'Barang Mutasi Eksternal',
                    'nibar'       => '-',
                    'kode_108'    => $kode108,
                    'kondisi'     => $m->kondisi ?: 'Baik',
                ];
            }

            $itemCount = count($itemsMapped);
            $deletedAt = $m->deleted_at ? Carbon::parse($m->deleted_at)->timezone('Asia/Jakarta') : null;
            $nilaiReal = (float) ($m->nilai_perolehan ?: ($astap?->total_realisasi ?: 0));
            $firstRegister = $registers->first();

            return [
                'id'                        => $m->id,
                'astap_id'                  => $m->astap_id,
                'kode'                      => $nomorBamb,
                'nomor_bamb'                => $nomorBamb,
                'jenis'                     => $m->jenis_mutasi ?: 'Transfer Antar-OPD',
                'tipe'                      => $m->tipe ?: 'masuk',
                'nama'                      => ($astap?->nama_barang ?: 'Barang Mutasi Eksternal') . ($itemCount > 1 ? " (+{$itemCount} unit)" : ''),
                'kode_barang'               => $firstRegister?->nibar ?: ($kode108 ?: '-'),
                'item_count'                => $itemCount,
                'items'                     => $itemsMapped,
                'asal'                      => $m->opd_asal ?: ($astap?->mutasi_asal ?: 'SKPD / Instansi Luar'),
                'opd_asal'                  => $m->opd_asal ?: ($astap?->mutasi_asal ?: 'SKPD / Instansi Luar'),
                'tujuan'                    => $m->unit?->nama ?: ($m->ruangan_tujuan ?: 'RSUD dr. H. Koesnadi'),
                'ruangan_tujuan'            => $m->unit?->nama ?: ($m->ruangan_tujuan ?: 'RSUD dr. H. Koesnadi'),
                'pemohon'                   => $m->pj_asal_nama ?: 'Pejabat OPD Pengirim',
                'penerima_pj'               => $m->pj_tujuan_nama ?: 'Pengurus Barang RSUD',
                'status_terakhir'           => $m->status ?: 'Disahkan (Selesai)',
                'alasan_mutasi'             => $m->alasan_mutasi ?: '-',
                'nilai_perolehan'           => $nilaiReal,
                'nilai_perolehan_formatted' => 'Rp ' . number_format($nilaiReal, 0, ',', '.'),
                'dokumen_lampiran'          => $m->dokumen_lampiran,
                'is_eksternal'              => true,
                'deleted_by'                => $m->deleted_by ?: 'Administrator',
                'deleted_at'                => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative'       => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'            => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 2. DATA TERHAPUS: MASTER ASTAP
        // =========================================================================
        $rawDeletedAstaps = Astap::onlyDeleted()
            ->with(['jenisAstap', 'rekeningBelanja', 'registers.unit'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedAstaps = $rawDeletedAstaps->map(function ($a) {
            $regCount = $a->registers->count();
            $firstReg = $a->registers->first();
            $deletedAt = $a->deleted_at ? Carbon::parse($a->deleted_at)->timezone('Asia/Jakarta') : null;

            $registersMapped = $a->registers->map(function ($r, $idx) {
                return [
                    'no'          => $idx + 1,
                    'id'          => $r->id,
                    'nibar'       => $r->nibar ?: ($r->no_register ?: '-'),
                    'ruang'       => $r->ruang_pemegang ?: ($r->unit?->nama ?: 'Gudang Perbekalan'),
                    'kondisi'     => $r->kondisi ?: 'Baik',
                    'status'      => $r->status ?: 'Tersedia',
                ];
            })->values()->toArray();

            return [
                'id'                  => $a->id,
                'kode'                => $a->kode_108 ?: ($a->spk_nomor ?: 'ASTAP-' . $a->id),
                'nama'                => $a->nama_barang ?: 'Aset Tetap',
                'tahun'               => $a->tahun_perolehan ?: '-',
                'volume'              => $a->jumlah_volume . ' ' . ($a->satuan ?: 'Unit'),
                'total_realisasi'     => 'Rp ' . number_format($a->total_realisasi ?: ($a->jumlah_anggaran ?: 0), 0, ',', '.'),
                'harga_satuan'        => 'Rp ' . number_format($a->harga_satuan ?: 0, 0, ',', '.'),
                'category'            => $a->category ?: 'KIB B',
                'penyedia'            => $a->penyedia_nama ?: '-',
                'spk_nomor'           => $a->spk_nomor ?: '-',
                'rekening'            => $a->rekeningBelanja?->nama_belanja ?: '-',
                'item_count'          => $regCount,
                'items'               => $registersMapped,
                'deleted_by'          => $a->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 2B. DATA TERHAPUS: REGISTER NIBAR INDIVIDUAL
        // =========================================================================
        // Hanya tampilkan register yang dihapus satuan dari paket pengadaan yang masih aktif di katalog
        $rawDeletedNibars = AstapRegister::onlyDeleted()
            ->whereHas('astap', function ($q) {
                $q->where('is_deleted', 0);
            })
            ->with(['astap.jenisAstap', 'unit'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedNibars = $rawDeletedNibars->map(function ($r) {
            $deletedAt = $r->deleted_at ? Carbon::parse($r->deleted_at)->timezone('Asia/Jakarta') : null;
            return [
                'id'                  => $r->id,
                'astap_id'            => $r->astap_id,
                'nibar'               => $r->nibar ?: ($r->no_register ?: 'REG-' . $r->id),
                'no_register'         => $r->no_register ?: '-',
                'nama_barang'         => $r->astap?->nama_barang ?? 'Aset ASTAP',
                'kode_108'            => $r->astap?->kode_108 ?: ($r->astap?->jenisAstap?->kode_108 ?: '-'),
                'kategori'            => $r->astap?->category ?: ($r->astap?->jenisAstap?->kategori ?? 'KIB B'),
                'ruang'               => $r->ruang_pemegang ?: ($r->unit?->nama ?: 'Gudang Perbekalan'),
                'kondisi'             => $r->kondisi ?: 'Baik',
                'status'              => $r->status ?: 'Tersedia',
                'spk_nomor'           => $r->astap?->spk_nomor ?: '-',
                'tahun'               => $r->astap?->tahun_perolehan ?: '-',
                'deleted_by'          => $r->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 3. DATA TERHAPUS: DISTRIBUSI ASET
        // =========================================================================
        $rawDeletedDistribusis = Distribusi::onlyDeleted()
            ->with(['unit', 'items.astap', 'items.registers.astapRegister'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedDistribusis = $rawDeletedDistribusis->map(function ($d) {
            $deletedAt = $d->deleted_at ? Carbon::parse($d->deleted_at)->timezone('Asia/Jakarta') : null;
            $tglCarbon = $d->tanggal_distribusi ? Carbon::parse($d->tanggal_distribusi)->timezone('Asia/Jakarta') : ($d->created_at ? Carbon::parse($d->created_at)->timezone('Asia/Jakarta') : null);
            $totalQty  = $d->items->sum('qty');
            $itemsMapped = $d->items->map(function ($it, $idx) {
                $nibars = $it->registers->map(fn($r) => $r->astapRegister?->nibar ?: '-')->filter()->implode(', ');
                return [
                    'no'          => $idx + 1,
                    'nama_barang' => $it->astap?->nama_barang ?? '-',
                    'qty'         => $it->qty,
                    'nibar_list'  => $nibars ?: '-',
                ];
            })->toArray();

            return [
                'id'                  => $d->id,
                'kode'                => $d->kode,
                'bast_nomor'          => $d->bast_nomor ?: '-',
                'tujuan'              => $d->unit?->nama ?? '-',
                'tanggal'             => $tglCarbon ? $tglCarbon->locale('id')->translatedFormat('d M Y') : '-',
                'total_qty'           => $totalQty . ' Unit',
                'pj_nama'             => $d->unit?->kepala ?? '-',
                'pj_nip'              => $d->unit?->nip ?? '-',
                'status'              => $d->status,
                'signed'              => (bool)$d->signed,
                'item_count'          => $d->items->count(),
                'items'               => $itemsMapped,
                'keterangan'          => $d->keterangan ?: '-',
                'deleted_by'          => $d->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 4. DATA TERHAPUS: HIBAH ASET (MASUK & KELUAR)
        // =========================================================================
        $rawDeletedHibahs = AstapHibah::onlyDeleted()
            ->with(['astap.jenisAstap', 'register', 'user'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedHibahs = $rawDeletedHibahs->map(function ($h) {
            $deletedAt = $h->deleted_at ? Carbon::parse($h->deleted_at)->timezone('Asia/Jakarta') : null;
            $tglBast = $h->tanggal_bast ? Carbon::parse($h->tanggal_bast)->timezone('Asia/Jakarta') : null;

            $namaBarang = $h->astap?->nama_barang ?: ($h->nama_barang ?: 'Barang Hibah');
            $kodeBarang = $h->astap?->kode_108 ?: ($h->astap?->jenisAstap?->sub_sub_rincian_objek ?: '-');

            return [
                'id'                  => $h->id,
                'nomor_bast'          => $h->nomor_bast,
                'tipe_hibah'          => $h->tipe_hibah, // 'masuk' atau 'keluar'
                'pihak_hibah'         => $h->pihak_hibah,
                'nama_barang'         => $namaBarang,
                'kode_barang'         => $kodeBarang,
                'volume'              => $h->jumlah_volume . ' ' . ($h->satuan ?: 'Unit'),
                'nilai_aset'          => (float) $h->nilai_aset,
                'nilai_aset_rp'       => 'Rp ' . number_format($h->nilai_aset ?: 0, 0, ',', '.'),
                'tanggal_bast'        => $tglBast ? $tglBast->locale('id')->translatedFormat('d M Y') : '-',
                'tahun'               => $h->tahun ?: '-',
                'triwulan'            => $h->triwulan ?: '-',
                'keterangan'          => $h->keterangan ?: '-',
                'alasan_hapus'        => $h->alasan_hapus ?: 'Dibatalkan',
                'deleted_by'          => $h->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 5. DATA TERHAPUS: UNIT & PAVILIUN
        // =========================================================================
        $rawDeletedUnits = Unit::onlyDeleted()
            ->with('user')
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedUnits = $rawDeletedUnits->map(function ($u) {
            $deletedAt = $u->deleted_at ? Carbon::parse($u->deleted_at)->timezone('Asia/Jakarta') : null;
            $bastCount = \App\Models\Distribusi::where('unit_id', $u->id)->count();
            return [
                'id'                  => $u->id,
                'kode'                => $u->kode_unit ?: 'UNIT-' . $u->id,
                'nama'                => $u->nama,
                'tipe'                => $u->tipe ?: 'Ruangan / Instalasi',
                'kepala'              => $u->kepala ?: '-',
                'nip'                 => $u->nip ?: '-',
                'email'               => $u->email ?: ($u->user?->email ?: '-'),
                'total_aset'          => $u->total_aset ?: 0,
                'total_bast'          => $bastCount,
                'deleted_by'          => $u->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 6. DATA TERHAPUS: AKUN PENGGUNA (USERS)
        // =========================================================================
        $rawDeletedUsers = User::onlyDeleted()
            ->with('unitModel')
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedUsers = $rawDeletedUsers->map(function ($u) {
            $deletedAt = $u->deleted_at ? Carbon::parse($u->deleted_at)->timezone('Asia/Jakarta') : null;
            return [
                'id'                  => $u->id,
                'name'                => $u->name,
                'email'               => $u->email,
                'role'                => $u->role,
                'role_label'          => $u->role === 'master_admin' ? '👑 Master Admin' : ($u->role === 'admin' ? '🛡️ Admin Operasional' : '🏥 Sub Admin Unit'),
                'unit'                => $u->unit ?: ($u->unitModel?->nama ?: '-'),
                'nip'                 => $u->nip ?: '-',
                'penugasan'           => $u->penugasan ?: '-',
                'status'              => $u->status ?: 'Aktif',
                'permissions'         => $u->permissions ?? [],
                'deleted_by'          => $u->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 7. DATA TERHAPUS: KEMITRAAN ASET (AKUN 1.5.2 / KSO)
        // =========================================================================
        $rawDeletedKemitraans = AstapKemitraan::onlyDeleted()
            ->with(['astap.jenisAstap', 'astap.registers.unit', 'astap.unit', 'user'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedKemitraans = $rawDeletedKemitraans->map(function ($k) {
            $deletedAt  = $k->deleted_at ? Carbon::parse($k->deleted_at)->timezone('Asia/Jakarta') : null;
            $tglPks     = $k->tanggal_pks ? Carbon::parse($k->tanggal_pks)->timezone('Asia/Jakarta') : null;
            $tglMulai   = $k->tanggal_mulai ? Carbon::parse($k->tanggal_mulai)->timezone('Asia/Jakarta') : null;
            $tglSelesai = $k->tanggal_selesai ? Carbon::parse($k->tanggal_selesai)->timezone('Asia/Jakarta') : null;

            $namaBarang = $k->astap?->nama_barang ?: 'Barang Kemitraan';
            $kodeBarang = $k->astap?->kode_108 ?: ($k->astap?->jenisAstap?->sub_sub_rincian_objek ?: '-');
            $ruangan    = $k->astap?->unit?->nama ?: '-';

            $registersMapped = $k->astap?->registers?->map(function ($r, $idx) {
                return [
                    'no'          => $idx + 1,
                    'nibar'       => $r->nibar ?: '-',
                    'nama_barang' => $r->nama_barang ?: '-',
                    'ruangan'     => $r->unit?->nama ?: ($r->ruang_pemegang ?: '-'),
                    'kondisi'     => $r->kondisi ?: 'Baik',
                ];
            })->toArray() ?? [];

            return [
                'id'                  => $k->id,
                'astap_id'            => $k->astap_id,
                'nomor_pks'           => $k->nomor_pks,
                'mitra_nama'          => $k->mitra_nama,
                'skema_kemitraan'     => $k->skema_kemitraan ?: 'KSO',
                'nama_barang'         => $namaBarang,
                'kode_barang'         => $kodeBarang,
                'volume'              => $k->jumlah_volume . ' ' . ($k->satuan ?: 'Unit'),
                'nilai_aset'          => (float) $k->nilai_aset,
                'nilai_aset_rp'       => 'Rp ' . number_format($k->nilai_aset ?: 0, 0, ',', '.'),
                'tanggal_pks'         => $tglPks ? $tglPks->locale('id')->translatedFormat('d M Y') : '-',
                'tanggal_mulai'       => $tglMulai ? $tglMulai->locale('id')->translatedFormat('d M Y') : '-',
                'tanggal_selesai'     => $tglSelesai ? $tglSelesai->locale('id')->translatedFormat('d M Y') : '-',
                'status_konsesi'      => $k->status_konsesi ?: 'Aktif',
                'ruangan'             => $ruangan,
                'tahun'               => $k->tahun ?: '-',
                'triwulan'            => $k->triwulan ?: '-',
                'keterangan'          => $k->keterangan ?: '-',
                'alasan_hapus'        => $k->alasan_hapus ?: 'Dihapus dari Kelola Kemitraan',
                'registers'           => $registersMapped,
                'deleted_by'          => $k->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 8. STATISTIK TERPUSAT SELURUH MODUL
        // =========================================================================
        $now = now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sevenDaysAgo  = $now->copy()->subDays(7);

        $mutasiInternalCount  = $deletedMutasis->count();
        $mutasiEksternalCount = $deletedMutasiEksternals->count();
        $mutasiCount          = $mutasiInternalCount + $mutasiEksternalCount;
        $astapCount      = $deletedAstaps->count();
        $nibarCount      = $deletedNibars->count();
        $distribusiCount = $deletedDistribusis->count();
        $hibahCount      = $deletedHibahs->count();
        $kemitraanCount  = $deletedKemitraans->count();
        $unitCount       = $deletedUnits->count();
        $userCount       = $deletedUsers->count();

        $totalAllDeleted = $mutasiCount + $astapCount + $nibarCount + $distribusiCount + $hibahCount + $kemitraanCount + $unitCount + $userCount;

        // Hitung 30 hari terakhir
        $filterMonth = fn($col) => $col->filter(fn($m) => $m->deleted_at && Carbon::parse($m->deleted_at)->gte($thirtyDaysAgo))->count();
        $totalThisMonth = $filterMonth($rawDeletedMutasis)
            + $filterMonth($rawDeletedMutasiEksternals)
            + $filterMonth($rawDeletedAstaps)
            + $filterMonth($rawDeletedNibars)
            + $filterMonth($rawDeletedDistribusis)
            + $filterMonth($rawDeletedHibahs)
            + $filterMonth($rawDeletedKemitraans)
            + $filterMonth($rawDeletedUnits)
            + $filterMonth($rawDeletedUsers);

        // Hitung 7 hari terakhir
        $filterWeek = fn($col) => $col->filter(fn($m) => $m->deleted_at && Carbon::parse($m->deleted_at)->gte($sevenDaysAgo))->count();
        $totalThisWeek = $filterWeek($rawDeletedMutasis)
            + $filterWeek($rawDeletedMutasiEksternals)
            + $filterWeek($rawDeletedAstaps)
            + $filterWeek($rawDeletedNibars)
            + $filterWeek($rawDeletedDistribusis)
            + $filterWeek($rawDeletedHibahs)
            + $filterWeek($rawDeletedKemitraans)
            + $filterWeek($rawDeletedUnits)
            + $filterWeek($rawDeletedUsers);

        $moduleStats = [
            'mutasi' => [
                'name'            => 'Mutasi Aset',
                'icon'            => '🔄',
                'count'           => $mutasiCount,
                'internal_count'  => $mutasiInternalCount,
                'eksternal_count' => $mutasiEksternalCount,
                'color'           => 'amber',
                'ready'           => true,
            ],
            'astap' => [
                'name'        => 'Master ASTAP',
                'icon'        => '📦',
                'count'       => $astapCount,
                'nibar_count' => $nibarCount,
                'color'       => 'emerald',
                'ready'       => true,
            ],
            'distribusi' => [
                'name'  => 'Distribusi Aset',
                'icon'  => '🚚',
                'count' => $distribusiCount,
                'color' => 'teal',
                'ready' => true,
            ],
            'hibah' => [
                'name'  => 'Hibah Aset',
                'icon'  => '🎁',
                'count' => $hibahCount,
                'color' => 'purple',
                'ready' => true,
            ],
            'kemitraan' => [
                'name'  => 'Kemitraan Aset',
                'icon'  => '🤝',
                'count' => $kemitraanCount,
                'color' => 'cyan',
                'ready' => true,
            ],
            'unit' => [
                'name'  => 'Unit & Paviliun',
                'icon'  => '🏥',
                'count' => $unitCount,
                'color' => 'indigo',
                'ready' => true,
            ],
            'users' => [
                'name'  => 'Akun Pengguna',
                'icon'  => '👥',
                'count' => $userCount,
                'color' => 'rose',
                'ready' => true,
            ],
        ];

        return view('pages.recycle_bin', compact(
            'deletedMutasis',
            'deletedMutasiEksternals',
            'deletedAstaps',
            'deletedNibars',
            'deletedDistribusis',
            'deletedHibahs',
            'deletedKemitraans',
            'deletedUnits',
            'deletedUsers',
            'activeTab',
            'moduleStats',
            'totalAllDeleted',
            'totalThisMonth',
            'totalThisWeek'
        ));
    }

    /**
     * Pulihkan 1 data berdasarkan modul (Restore Single)
     */
    public function restore(Request $request, string $module, int $id)
    {
        switch ($module) {
            case 'mutasi':
            case 'mutasi_internal':
                $mutasi = AstapMutasi::findOrFail($id);
                $bamb = $mutasi->nomor_bamb;
                $mutasi->restoreData();
                $msg = "Data Berita Acara Mutasi {$bamb} berhasil dipulihkan ke status aktif.";
                break;

            case 'mutasi_eksternal':
                $mEksternal = \App\Models\MutasiEksternal::findOrFail($id);
                $bamb = $mEksternal->nomor_bamb;
                DB::transaction(function () use ($mEksternal) {
                    $mEksternal->restoreData();
                    if ($mEksternal->astap) {
                        $mEksternal->astap->restoreData();
                        $mEksternal->astap->registers()->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                        $mEksternal->astap->jumlah_volume = max(1, $mEksternal->astap->registers()->where('is_deleted', 0)->count());
                        $mEksternal->astap->save();
                    }
                });
                $msg = "Data Berita Acara Mutasi Eksternal {$bamb} beserta aset terkait berhasil dipulihkan ke katalog aktif.";
                break;

            case 'astap':
                $astap = Astap::findOrFail($id);
                $nama = $astap->nama_barang ?: 'Aset ASTAP';
                DB::transaction(function () use ($astap) {
                    $astap->restoreData();
                    $astap->registers()->update([
                        'is_deleted'    => 0,
                        'deleted_by'    => null,
                        'deleted_by_id' => null,
                        'deleted_at'    => null,
                    ]);
                    if ($astap->mutasiEksternal) {
                        $astap->mutasiEksternal->restoreData();
                    }
                    // Sinkronkan jumlah volume paket dengan total register aktif
                    $astap->jumlah_volume = max(1, $astap->registers()->where('is_deleted', 0)->count());
                    $astap->save();
                });
                $msg = "Data Master Aset ASTAP \"{$nama}\" dan seluruh register NIBAR berhasil dipulihkan.";
                break;

            case 'distribusi':
                $distribusi = Distribusi::with('unit')->findOrFail($id);
                $kode = $distribusi->kode;
                $unitId = $distribusi->unit_id;
                $unitNama = $distribusi->unit?->nama ?? 'Ruangan RSUD';
                $userName = auth()->user()?->name ?? 'Admin';

                DB::transaction(function () use ($distribusi) {
                    $distribusi->restoreData();
                    // Kembalikan register terkait ke unit jika status terdistribusi
                    if (in_array($distribusi->status, ['Dalam Pengiriman', 'Telah Diterima', 'Diterima'])) {
                        foreach ($distribusi->items as $it) {
                            $regIds = $it->registers->pluck('astap_register_id')->filter()->toArray();
                            if (!empty($regIds) && $distribusi->unit_id) {
                                AstapRegister::whereIn('id', $regIds)->update([
                                    'unit_id'        => $distribusi->unit_id,
                                    'ruang_pemegang' => $distribusi->unit?->nama ?? 'Ruangan Unit',
                                    'status'         => 'Terdistribusi',
                                ]);
                            }
                        }
                    }
                });

                // Bersihkan notifikasi pembatalan lama & kirim notifikasi pemulihan
                try {
                    \App\Models\SystemNotification::where('message', 'LIKE', "%{$kode}%")->delete();

                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Distribusi Dipulihkan: {$unitNama}",
                        "{$kode} telah dipulihkan dari Tong Sampah oleh {$userName}",
                        'distribusi',
                        route('distribusi.index')
                    );

                    if ($unitId) {
                        \App\Services\NotificationService::sendToUnitSubAdmin(
                            $unitId,
                            $unitNama,
                            "Distribusi Aktif Kembali: {$unitNama}",
                            "{$kode} telah dipulihkan & aktif kembali",
                            'distribusi',
                            route('distribusi.index')
                        );
                    }
                } catch (\Throwable $e) {}

                $msg = "Transaksi Distribusi Aset {$kode} berhasil dipulihkan ke status aktif.";
                break;

            case 'hibah':
                $hibah = AstapHibah::findOrFail($id);
                $nomorBast = $hibah->nomor_bast;
                DB::transaction(function () use ($hibah) {
                    $hibah->restoreData();

                    // Jika yang dipulihkan adalah hibah KELUAR, tandai kembali unit register & astap sebagai dihibahkan
                    if ($hibah->tipe_hibah === 'keluar') {
                        $user = Auth::user();
                        $alasan = 'Dihibahkan ke ' . $hibah->pihak_hibah . ' (BAST: ' . $hibah->nomor_bast . ')';

                        if ($hibah->astap_register_id) {
                            AstapRegister::where('id', $hibah->astap_register_id)->update([
                                'status'     => 'Dihibahkan',
                                'is_deleted' => 1,
                                'deleted_at' => now(),
                                'deleted_by' => $user?->name ?: 'Administrator',
                            ]);
                        } else {
                            AstapRegister::where('astap_id', $hibah->astap_id)->update([
                                'status'     => 'Dihibahkan',
                                'is_deleted' => 1,
                                'deleted_at' => now(),
                                'deleted_by' => $user?->name ?: 'Administrator',
                            ]);
                        }

                        $astap = Astap::find($hibah->astap_id);
                        if ($astap) {
                            $sisaAktif = AstapRegister::where('astap_id', $astap->id)->where('is_deleted', 0)->count();
                            if ($sisaAktif === 0) {
                                $astap->update([
                                    'is_deleted'          => 1,
                                    'deleted_at'          => now(),
                                    'deleted_by'          => $user?->name ?: 'Administrator',
                                    'keterangan_tambahan' => $alasan,
                                ]);
                            } else {
                                $astap->update(['jumlah_volume' => $sisaAktif]);
                            }
                        }
                    }
                });
                $msg = "Catatan Transaksi Hibah {$nomorBast} berhasil dipulihkan ke daftar aktif.";
                break;

            case 'kemitraan':
                $kemitraan = AstapKemitraan::findOrFail($id);
                $nomorPks = $kemitraan->nomor_pks;
                DB::transaction(function () use ($kemitraan) {
                    $kemitraan->restoreData();
                    if ($kemitraan->astap) {
                        $kemitraan->astap->restoreData();
                        $kemitraan->astap->registers()->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                        $kemitraan->astap->jumlah_volume = max(1, $kemitraan->astap->registers()->where('is_deleted', 0)->count());
                        $kemitraan->astap->save();
                    }
                });
                $msg = "Data Kerja Sama Kemitraan PKS {$nomorPks} beserta aset terkait berhasil dipulihkan ke daftar aktif.";
                break;

            case 'unit':
                $unit = Unit::findOrFail($id);
                $nama = $unit->nama;
                $unit->restoreData();
                $msg = "Data Unit / Paviliun \"{$nama}\" dan seluruh akun pengguna terkait berhasil dipulihkan ke katalog aktif.";
                break;

            case 'nibar':
                $reg = AstapRegister::findOrFail($id);
                $nibar = $reg->nibar ?: $reg->no_register;
                $astap = $reg->astap;
                $astapRestored = false;

                DB::transaction(function () use ($reg, $astap, &$astapRestored) {
                    $reg->restoreData();
                    // Jika paket induk sedang berstatus terhapus, ikut pulihkan paket induk agar data tidak menjadi orphan
                    if ($astap && $astap->is_deleted) {
                        $astap->restoreData();
                        if ($astap->mutasiEksternal) {
                            $astap->mutasiEksternal->restoreData();
                        }
                        $astapRestored = true;
                    }
                    if ($astap) {
                        $this->syncAstapAfterRegisterChange($astap);
                    }
                });

                if ($astapRestored) {
                    $msg = "Unit Register NIBAR \"{$nibar}\" dan paket pengadaan induknya \"{$astap->nama_barang}\" berhasil dipulihkan ke katalog aktif.";
                } elseif ($astap) {
                    $msg = "Unit Register NIBAR \"{$nibar}\" berhasil dipulihkan ke paket pengadaan \"{$astap->nama_barang}\" (Volume aktif: {$astap->jumlah_volume}).";
                } else {
                    $msg = "Unit Register NIBAR \"{$nibar}\" berhasil dipulihkan ke katalog aktif.";
                }
                break;

            case 'users':
            case 'user':
                $targetUser = User::findOrFail($id);
                $nama = $targetUser->name;
                $email = $targetUser->email;
                if (User::active()->where('email', $email)->where('id', '!=', $targetUser->id)->exists()) {
                    $conflictMsg = "Gagal memulihkan: Email \"{$email}\" saat ini sudah digunakan oleh akun pengguna aktif lain.";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $conflictMsg], 422);
                    }
                    return back()->with('error', $conflictMsg);
                }
                $targetUser->restoreData();
                $msg = "Akun Pengguna \"{$nama}\" ({$email}) berhasil dipulihkan ke status aktif.";
                break;

            default:
                return back()->with('error', "Modul {$module} tidak dikenal untuk pemulihan data.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json([
                'success'        => true,
                'message'        => $msg,
                'astap_restored' => $astapRestored ?? false,
                'astap_id'       => isset($astap) && ($astapRestored ?? false) ? $astap->id : null,
            ]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module === 'nibar' ? 'astap' : $module])->with('success', $msg);
    }

    /**
     * Pulihkan banyak data sekaligus (Bulk Restore)
     */
    public function bulkRestore(Request $request, string $module)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            $msg = 'Pilih minimal satu data yang ingin dipulihkan.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $restoredCount = 0;
        switch ($module) {
            case 'mutasi':
            case 'mutasi_internal':
                $items = AstapMutasi::whereIn('id', $ids)->get();
                foreach ($items as $item) {
                    $item->restoreData();
                    $restoredCount++;
                }
                $msg = "Sebanyak {$restoredCount} transaksi mutasi internal berhasil dipulihkan ke status aktif.";
                break;

            case 'mutasi_eksternal':
                $items = \App\Models\MutasiEksternal::whereIn('id', $ids)->get();
                DB::transaction(function () use ($items, &$restoredCount) {
                    foreach ($items as $mEksternal) {
                        $mEksternal->restoreData();
                        if ($mEksternal->astap) {
                            $mEksternal->astap->restoreData();
                            $mEksternal->astap->registers()->update([
                                'is_deleted'    => 0,
                                'deleted_by'    => null,
                                'deleted_by_id' => null,
                                'deleted_at'    => null,
                            ]);
                            $mEksternal->astap->jumlah_volume = max(1, $mEksternal->astap->registers()->where('is_deleted', 0)->count());
                            $mEksternal->astap->save();
                        }
                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} transaksi mutasi eksternal berhasil dipulihkan ke status aktif.";
                break;

            case 'astap':
                $astaps = Astap::whereIn('id', $ids)->get();
                DB::transaction(function () use ($astaps, &$restoredCount) {
                    foreach ($astaps as $a) {
                        $a->restoreData();
                        $a->registers()->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                        if ($a->mutasiEksternal) {
                            $a->mutasiEksternal->restoreData();
                        }
                        $a->jumlah_volume = max(1, $a->registers()->where('is_deleted', 0)->count());
                        $a->save();
                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} data Master ASTAP berhasil dipulihkan ke status aktif.";
                break;

            case 'distribusi':
                $distribusis = Distribusi::whereIn('id', $ids)->get();
                DB::transaction(function () use ($distribusis, &$restoredCount) {
                    foreach ($distribusis as $d) {
                        $d->restoreData();
                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} transaksi distribusi berhasil dipulihkan ke status aktif.";
                break;

            case 'hibah':
                $hibahs = AstapHibah::whereIn('id', $ids)->get();
                DB::transaction(function () use ($hibahs, &$restoredCount) {
                    $user = Auth::user();
                    foreach ($hibahs as $hibah) {
                        $hibah->restoreData();
                        if ($hibah->tipe_hibah === 'keluar') {
                            $alasan = 'Dihibahkan ke ' . $hibah->pihak_hibah . ' (BAST: ' . $hibah->nomor_bast . ')';
                            if ($hibah->astap_register_id) {
                                AstapRegister::where('id', $hibah->astap_register_id)->update([
                                    'status'     => 'Dihibahkan',
                                    'is_deleted' => 1,
                                    'deleted_at' => now(),
                                    'deleted_by' => $user?->name ?: 'Administrator',
                                ]);
                            } else {
                                AstapRegister::where('astap_id', $hibah->astap_id)->update([
                                    'status'     => 'Dihibahkan',
                                    'is_deleted' => 1,
                                    'deleted_at' => now(),
                                    'deleted_by' => $user?->name ?: 'Administrator',
                                ]);
                            }
                            $astap = Astap::find($hibah->astap_id);
                            if ($astap) {
                                $sisaAktif = AstapRegister::where('astap_id', $astap->id)->where('is_deleted', 0)->count();
                                if ($sisaAktif === 0) {
                                    $astap->update([
                                        'is_deleted'          => 1,
                                        'deleted_at'          => now(),
                                        'deleted_by'          => $user?->name ?: 'Administrator',
                                        'keterangan_tambahan' => $alasan,
                                    ]);
                                } else {
                                    $astap->update(['jumlah_volume' => $sisaAktif]);
                                }
                            }
                        }
                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} transaksi hibah aset berhasil dipulihkan ke daftar aktif.";
                break;

            case 'kemitraan':
                $kemitraans = AstapKemitraan::whereIn('id', $ids)->get();
                DB::transaction(function () use ($kemitraans, &$restoredCount) {
                    foreach ($kemitraans as $k) {
                        $k->restoreData();
                        if ($k->astap) {
                            $k->astap->restoreData();
                            $k->astap->registers()->update([
                                'is_deleted'    => 0,
                                'deleted_by'    => null,
                                'deleted_by_id' => null,
                                'deleted_at'    => null,
                            ]);
                            $k->astap->jumlah_volume = max(1, $k->astap->registers()->where('is_deleted', 0)->count());
                            $k->astap->save();
                        }
                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} dokumen kemitraan aset berhasil dipulihkan ke status aktif.";
                break;

            case 'unit':
                $units = Unit::whereIn('id', $ids)->get();
                foreach ($units as $u) {
                    $u->restoreData();
                    $restoredCount++;
                }
                $msg = "Sebanyak {$restoredCount} Unit & Paviliun beserta akun pengguna terkait berhasil dipulihkan ke katalog aktif.";
                break;

            case 'nibar':
                $regs = AstapRegister::whereIn('id', $ids)->get();
                $astapParents = [];
                $parentRestoredCount = 0;

                DB::transaction(function () use ($regs, &$astapParents, &$restoredCount, &$parentRestoredCount) {
                    foreach ($regs as $r) {
                        $r->restoreData();
                        if ($r->astap) {
                            if ($r->astap->is_deleted && !isset($astapParents[$r->astap_id])) {
                                $r->astap->restoreData();
                                $parentRestoredCount++;
                            }
                            $astapParents[$r->astap_id] = $r->astap;
                        }
                        $restoredCount++;
                    }
                    foreach ($astapParents as $parent) {
                        $this->syncAstapAfterRegisterChange($parent);
                    }
                });

                $msg = "Sebanyak {$restoredCount} unit register NIBAR berhasil dipulihkan ke katalog aktif.";
                if ($parentRestoredCount > 0) {
                    $msg .= " ({$parentRestoredCount} paket pengadaan induk otomatis diaktifkan kembali).";
                }
                break;

            case 'users':
            case 'user':
                $users = User::whereIn('id', $ids)->get();
                foreach ($users as $u) {
                    if (!User::active()->where('email', $u->email)->where('id', '!=', $u->id)->exists()) {
                        $u->restoreData();
                        $restoredCount++;
                    }
                }
                $msg = "Sebanyak {$restoredCount} akun pengguna berhasil dipulihkan ke status aktif.";
                break;

            default:
                return back()->with('error', "Modul {$module} tidak dikenal untuk pemulihan massal.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json([
                'success'             => true,
                'message'             => $msg,
                'count'               => $restoredCount,
                'restored_parent_ids' => isset($astapParents) ? array_keys($astapParents) : [],
            ]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module])->with('success', $msg);
    }

    /**
     * Hapus permanen massal untuk item terpilih (Bulk Force Delete)
     */
    public function bulkForceDelete(Request $request, string $module)
    {
        $userRole = Auth::user()->role ?? 'user';
        if (!in_array($userRole, ['admin', 'master_admin'])) {
            $msg = 'Hanya Administrator yang memiliki wewenang untuk menghapus data permanen.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu data untuk dihapus permanen.');
        }

        $deletedCount = 0;
        switch ($module) {
            case 'mutasi':
            case 'mutasi_internal':
                $mutasis = AstapMutasi::whereIn('id', $ids)->get();
                foreach ($mutasis as $m) {
                    AstapMutasiRegister::where('astap_mutasi_id', $m->id)->delete();
                    $m->delete();
                    $deletedCount++;
                }
                $msg = "Sebanyak {$deletedCount} data mutasi internal telah dihapus permanen dari database.";
                break;

            case 'mutasi_eksternal':
                $items = \App\Models\MutasiEksternal::whereIn('id', $ids)->get();
                DB::transaction(function () use ($items, &$deletedCount) {
                    foreach ($items as $mEksternal) {
                        $astap = $mEksternal->astap;
                        if ($astap) {
                            $astap->registers()->delete();
                            if ($astap->pelimpahanSkpd) {
                                $astap->pelimpahanSkpd->delete();
                            }
                            $mEksternal->delete();
                            $astap->delete();
                        } else {
                            $mEksternal->delete();
                        }
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} data mutasi eksternal telah dihapus permanen dari database.";
                break;

            case 'kemitraan':
                $kemitraans = AstapKemitraan::whereIn('id', $ids)->get();
                DB::transaction(function () use ($kemitraans, &$deletedCount) {
                    foreach ($kemitraans as $k) {
                        $astap = $k->astap;
                        if ($astap) {
                            $astap->registers()->delete();
                            $k->delete();
                            $astap->delete();
                        } else {
                            $k->delete();
                        }
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} data kemitraan aset telah dihapus permanen dari database.";
                break;

            case 'astap':
                $astaps = Astap::whereIn('id', $ids)->get();
                $blockedAstaps = [];
                foreach ($astaps as $a) {
                    $regIds = $a->registers()->pluck('id')->toArray();
                    if (!empty($regIds)) {
                        $hasDistribusi = \App\Models\DistribusiItemRegister::whereIn('astap_register_id', $regIds)->exists();
                        $hasMutasi = \App\Models\AstapMutasiRegister::whereIn('astap_register_id', $regIds)->exists();
                        if ($hasDistribusi || $hasMutasi) {
                            $blockedAstaps[] = $a->nama_barang ?: "ASTAP-{$a->id}";
                        }
                    }
                }
                if (!empty($blockedAstaps)) {
                    $listStr = implode(', ', array_slice($blockedAstaps, 0, 3));
                    if (count($blockedAstaps) > 3) {
                        $listStr .= '... dan ' . (count($blockedAstaps) - 3) . ' paket lainnya';
                    }
                    $msg = "Penghapusan permanen ditolak: Terdapat paket ASTAP [{$listStr}] yang unitnya memiliki riwayat transaksi BAST Distribusi atau Mutasi aset aktif yang dilindungi audit.";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->with('error', $msg);
                }

                DB::transaction(function () use ($astaps, &$deletedCount) {
                    foreach ($astaps as $a) {
                        $a->registers()->delete();
                        $a->delete();
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} data Master ASTAP telah dihapus permanen dari database.";
                break;

            case 'distribusi':
                $distribusis = Distribusi::whereIn('id', $ids)->get();
                DB::transaction(function () use ($distribusis, &$deletedCount) {
                    foreach ($distribusis as $d) {
                        $kode = $d->kode;
                        $d->delete();
                        try {
                            \App\Models\SystemNotification::where('message', 'LIKE', "%{$kode}%")->delete();
                        } catch (\Throwable $e) {}
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} transaksi distribusi telah dihapus permanen dari database.";
                break;

            case 'hibah':
                $hibahs = AstapHibah::whereIn('id', $ids)->get();
                foreach ($hibahs as $h) {
                    $h->delete();
                    $deletedCount++;
                }
                $msg = "Sebanyak {$deletedCount} data transaksi hibah aset telah dihapus permanen dari database.";
                break;

            case 'unit':
                $units = Unit::whereIn('id', $ids)->get();
                $unitsWithAssets = [];
                $unitsWithBasts = [];
                foreach ($units as $u) {
                    $count = (int) ($u->total_aset ?: \App\Models\AstapRegister::where('unit_id', $u->id)->count());
                    if ($count > 0) {
                        $unitsWithAssets[] = "{$u->nama} ({$count} aset)";
                    }
                    $bastCount = \App\Models\Distribusi::where('unit_id', $u->id)->count();
                    if ($bastCount > 0) {
                        $unitsWithBasts[] = "{$u->nama} ({$bastCount} arsip BAST)";
                    }
                }
                if (!empty($unitsWithAssets)) {
                    $listStr = implode(', ', array_slice($unitsWithAssets, 0, 3));
                    if (count($unitsWithAssets) > 3) {
                        $listStr .= '... dan ' . (count($unitsWithAssets) - 3) . ' unit lainnya';
                    }
                    $msg = "Penghapusan permanen ditolak: Terdapat unit yang masih memiliki aset aktif [{$listStr}]. Silakan mutasi seluruh aset ke ruangan lain terlebih dahulu.";
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success'    => false,
                            'message'    => $msg,
                            'action_url' => route('mutasi.index'),
                        ], 422);
                    }
                    return back()->with('error', $msg);
                }

                // PROTEKSI BAST AUDIT: Unit yang memiliki riwayat BAST Distribusi tidak boleh dihapus permanen
                if (!empty($unitsWithBasts)) {
                    $listStr = implode(', ', array_slice($unitsWithBasts, 0, 3));
                    if (count($unitsWithBasts) > 3) {
                        $listStr .= '... dan ' . (count($unitsWithBasts) - 3) . ' unit lainnya';
                    }
                    $msg = "Penghapusan permanen ditolak: Terdapat unit yang memiliki riwayat dokumen BAST Distribusi [{$listStr}]. Demi kepatuhan audit BPK & Inspektorat, unit dengan riwayat BAST tidak boleh dihapus dari database. Silakan pulihkan unit ini jika diperlukan.";
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success'    => false,
                            'message'    => $msg,
                            'action_url' => route('bast.index'),
                        ], 422);
                    }
                    return back()->with('error', $msg);
                }

                DB::transaction(function () use ($units, &$deletedCount) {
                    foreach ($units as $u) {
                        User::where('unit_id', $u->id)->delete();
                        $u->delete();
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} Unit & Paviliun telah dihapus permanen dari database.";
                break;

            case 'nibar':
                $regs = AstapRegister::whereIn('id', $ids)->get();
                $blockedNibars = [];
                foreach ($regs as $r) {
                    $hasDistribusi = \App\Models\DistribusiItemRegister::where('astap_register_id', $r->id)->exists();
                    $hasMutasi = \App\Models\AstapMutasiRegister::where('astap_register_id', $r->id)->exists();
                    if ($hasDistribusi || $hasMutasi) {
                        $blockedNibars[] = $r->nibar ?: $r->no_register;
                    }
                }
                if (!empty($blockedNibars)) {
                    $listStr = implode(', ', array_slice($blockedNibars, 0, 3));
                    if (count($blockedNibars) > 3) {
                        $listStr .= '... dan ' . (count($blockedNibars) - 3) . ' NIBAR lainnya';
                    }
                    $msg = "Penghapusan permanen ditolak: Terdapat register NIBAR [{$listStr}] yang memiliki riwayat transaksi distribusi/mutasi aktif yang dilindungi audit.";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->with('error', $msg);
                }

                $astapParents = [];
                foreach ($regs as $r) {
                    if ($r->astap) {
                        $astapParents[$r->astap_id] = $r->astap;
                    }
                    $r->delete();
                    $deletedCount++;
                }
                foreach ($astapParents as $parent) {
                    $this->syncAstapAfterRegisterChange($parent);
                }
                $msg = "Sebanyak {$deletedCount} register NIBAR telah dihapus permanen dari database.";
                break;

            case 'users':
            case 'user':
                $users = User::whereIn('id', $ids)->get();
                foreach ($users as $u) {
                    $u->delete();
                    $deletedCount++;
                }
                $msg = "Sebanyak {$deletedCount} akun pengguna telah dihapus permanen dari database.";
                break;

            default:
                return back()->with('error', "Modul {$module} tidak dikenal.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'count' => $deletedCount]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module === 'nibar' ? 'astap' : $module])->with('success', $msg);
    }

    /**
     * Hapus permanen dari database (Hard Delete - Khusus Admin / Master Admin)
     */
    public function forceDelete(Request $request, string $module, int $id)
    {
        $userRole = Auth::user()->role ?? 'user';
        if (!in_array($userRole, ['admin', 'master_admin'])) {
            $msg = 'Hanya Administrator yang memiliki wewenang untuk menghapus data secara permanen.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        switch ($module) {
            case 'mutasi':
            case 'mutasi_internal':
                $mutasi = AstapMutasi::findOrFail($id);
                $bamb = $mutasi->nomor_bamb;
                AstapMutasiRegister::where('astap_mutasi_id', $mutasi->id)->delete();
                $mutasi->delete();
                $msg = "Data Berita Acara Mutasi {$bamb} telah dihapus secara permanen dari database.";
                break;

            case 'mutasi_eksternal':
                $mEksternal = \App\Models\MutasiEksternal::findOrFail($id);
                $bamb = $mEksternal->nomor_bamb;
                DB::transaction(function () use ($mEksternal) {
                    $astap = $mEksternal->astap;
                    if ($astap) {
                        $astap->registers()->delete();
                        if ($astap->pelimpahanSkpd) {
                            $astap->pelimpahanSkpd->delete();
                        }
                        $mEksternal->delete();
                        $astap->delete();
                    } else {
                        $mEksternal->delete();
                    }
                });
                $msg = "Data Berita Acara Mutasi Eksternal {$bamb} telah dihapus secara permanen dari database.";
                break;

            case 'astap':
                $astap = Astap::findOrFail($id);
                $nama = $astap->nama_barang ?: 'Aset ASTAP';
                // Proteksi BAST Distribusi & Mutasi Aset untuk seluruh register di bawah paket ini
                $regIds = $astap->registers()->pluck('id')->toArray();
                if (!empty($regIds)) {
                    $hasDistribusi = \App\Models\DistribusiItemRegister::whereIn('astap_register_id', $regIds)->exists();
                    $hasMutasi = \App\Models\AstapMutasiRegister::whereIn('astap_register_id', $regIds)->exists();
                    if ($hasDistribusi || $hasMutasi) {
                        $reason = $hasDistribusi ? 'telah resmi diserahterimakan via dokumen BAST Distribusi' : 'memiliki riwayat mutasi aset';
                        $msg = "Penghapusan permanen ditolak: Paket ASTAP \"{$nama}\" memiliki unit yang {$reason}. Data dilindungi undang-undang untuk audit BPK & Inspektorat.";
                        if ($request->wantsJson()) {
                            return response()->json(['success' => false, 'message' => $msg], 422);
                        }
                        return back()->with('error', $msg);
                    }
                }

                DB::transaction(function () use ($astap) {
                    $astap->registers()->delete();
                    $astap->delete();
                });
                $msg = "Data Master ASTAP \"{$nama}\" telah dihapus secara permanen dari database.";
                break;

            case 'distribusi':
                $distribusi = Distribusi::findOrFail($id);
                $kode = $distribusi->kode;
                DB::transaction(function () use ($distribusi) {
                    $distribusi->delete();
                });
                // Bersihkan seluruh notifikasi terkait saat data dimusnahkan permanen
                try {
                    \App\Models\SystemNotification::where('message', 'LIKE', "%{$kode}%")->delete();
                } catch (\Throwable $e) {}

                $msg = "Transaksi Distribusi {$kode} telah dihapus secara permanen dari database.";
                break;

            case 'hibah':
                $hibah = AstapHibah::findOrFail($id);
                $bast = $hibah->nomor_bast;
                $hibah->delete();
                $msg = "Data transaksi hibah BAST {$bast} telah dihapus secara permanen dari database.";
                break;

            case 'kemitraan':
                $kemitraan = AstapKemitraan::findOrFail($id);
                $pks = $kemitraan->nomor_pks;
                DB::transaction(function () use ($kemitraan) {
                    $astap = $kemitraan->astap;
                    if ($astap) {
                        $astap->registers()->delete();
                        $kemitraan->delete();
                        $astap->delete();
                    } else {
                        $kemitraan->delete();
                    }
                });
                $msg = "Data Kemitraan PKS {$pks} beserta aset register terkait telah dihapus secara permanen dari database.";
                break;

            case 'unit':
                $unit = Unit::findOrFail($id);
                $nama = $unit->nama;
                $assetCount = (int) ($unit->total_aset ?: \App\Models\AstapRegister::where('unit_id', $unit->id)->count());
                if ($assetCount > 0) {
                    $msg = "Penghapusan permanen ditolak: Unit \"{$nama}\" masih memiliki {$assetCount} aset aktif. Silakan pulihkan unit ini lalu lakukan mutasi aset ke unit lain terlebih dahulu.";
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success'    => false,
                            'message'    => $msg,
                            'action_url' => route('mutasi.index'),
                        ], 422);
                    }
                    return back()->with('error', $msg);
                }

                // PROTEKSI BAST AUDIT: Unit yang memiliki riwayat dokumen BAST Distribusi tidak boleh dihapus
                $bastCount = \App\Models\Distribusi::where('unit_id', $unit->id)->count();
                if ($bastCount > 0) {
                    $msg = "Penghapusan permanen ditolak: Unit \"{$nama}\" memiliki {$bastCount} arsip dokumen BAST Distribusi resmi. Dokumen BAST dilindungi undang-undang untuk audit BPK & Inspektorat sehingga unit tidak boleh dihapus dari database. Silakan pulihkan unit ini jika diperlukan.";
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success'    => false,
                            'message'    => $msg,
                            'action_url' => route('bast.index'),
                        ], 422);
                    }
                    return back()->with('error', $msg);
                }

                DB::transaction(function () use ($unit) {
                    User::where('unit_id', $unit->id)->delete();
                    $unit->delete();
                });
                $msg = "Data Unit \"{$nama}\" dan akun terkait telah dihapus secara permanen dari database.";
                break;

            case 'nibar':
                $reg = AstapRegister::findOrFail($id);
                $nibar = $reg->nibar ?: $reg->no_register;
                $hasDistribusi = \App\Models\DistribusiItemRegister::where('astap_register_id', $reg->id)->exists();
                $hasMutasi = \App\Models\AstapMutasiRegister::where('astap_register_id', $reg->id)->exists();
                if ($hasDistribusi || $hasMutasi) {
                    $msg = "Penghapusan permanen ditolak: Register NIBAR \"{$nibar}\" memiliki riwayat transaksi BAST/mutasi aktif yang dilindungi audit.";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->with('error', $msg);
                }
                $parentAstap = $reg->astap;
                $reg->delete();
                $this->syncAstapAfterRegisterChange($parentAstap);
                $msg = "Register NIBAR \"{$nibar}\" telah dihapus secara permanen dari database.";
                break;

            case 'users':
            case 'user':
                $targetUser = User::findOrFail($id);
                $nama = $targetUser->name;
                $targetUser->delete();
                $msg = "Akun Pengguna \"{$nama}\" telah dihapus secara permanen dari sistem.";
                break;

            default:
                return back()->with('error', "Modul {$module} tidak dikenal untuk penghapusan permanen.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module === 'nibar' ? 'astap' : $module])->with('success', $msg);
    }

    /**
     * Sinkronisasi volume dan kondisi dominan parent ASTAP setelah status register berubah
     */
    private function syncAstapAfterRegisterChange($astap)
    {
        if (!$astap) return;
        $newCount = $astap->registers()->where('is_deleted', 0)->count();
        $astap->jumlah_volume = max(1, $newCount);

        $allRegs = $astap->registers()->where('is_deleted', 0)->get();
        $totalRegs = $allRegs->count();
        $baikCount = $allRegs->where('kondisi', 'Baik')->count();
        $kbCount = $allRegs->where('kondisi', 'Kurang Baik')->count();
        $rrCount = $allRegs->where('kondisi', 'Rusak Ringan')->count();
        $rbCount = $allRegs->whereIn('kondisi', ['Rusak Berat', 'Rusak'])->count();
        $dominan = ($baikCount >= $kbCount && $baikCount >= $rrCount && $baikCount >= $rbCount) ? 'Baik'
            : (($kbCount >= $rrCount && $kbCount >= $rbCount) ? 'Kurang Baik'
            : (($rrCount >= $rbCount) ? 'Rusak Ringan' : 'Rusak Berat'));

        $spec = $astap->spesifikasi_json ?? [];
        if (is_array($spec)) {
            $spec['kondisi'] = $dominan;
            $spec['kondisi_stats'] = [
                'total' => $totalRegs,
                'baik' => $baikCount,
                'kurang_baik' => $kbCount,
                'rusak_ringan' => $rrCount,
                'rusak_berat' => $rbCount,
                'pct_baik' => $totalRegs > 0 ? round($baikCount / $totalRegs * 100) : 0,
                'pct_kb' => $totalRegs > 0 ? round($kbCount / $totalRegs * 100) : 0,
                'pct_rr' => $totalRegs > 0 ? round($rrCount / $totalRegs * 100) : 0,
                'pct_rb' => $totalRegs > 0 ? round($rbCount / $totalRegs * 100) : 0,
                'kondisi_dominan' => $dominan,
            ];
            $astap->spesifikasi_json = $spec;
        }
        $astap->save();
    }
}

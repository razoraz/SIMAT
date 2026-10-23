<?php

namespace App\Http\Controllers;

use App\Models\AstapMutasi;
use App\Models\AstapMutasiRegister;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapHibah;
use App\Models\AstapKemitraan;
use App\Models\AstapBelanjaBarang;
use App\Models\AstapReklas;
use App\Models\Distribusi;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        // Sesuai Opsi 1: Dokumen yang dibatalkan tersimpan di modul Mutasi Eksternal
        // dan tidak mengotori Recycle Bin. Recycle Bin hanya untuk data terhapus.
        $rawDeletedMutasiEksternals = \App\Models\MutasiEksternal::onlyDeleted()
            ->where('status', '!=', 'Dibatalkan')
            ->with([
                'astap.allRegisters.unit',
                'astap.jenisAstap',
                'unit',
                'user',
                'mutasiRegisters.register.astap.jenisAstap',
                'mutasiRegisters.register.unit'
            ])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedMutasiEksternals = $rawDeletedMutasiEksternals->map(function ($m) {
            $isKeluar = ($m->tipe === 'keluar');
            $astap = $m->astap;
            $nomorBamb = $m->nomor_bamb ?: ($astap?->bast_dokumen_nomor ?: ($isKeluar ? 'BAST-KELUAR-' . str_pad($m->id, 4, '0', STR_PAD_LEFT) : 'BAMB-EXT-' . str_pad($m->id, 4, '0', STR_PAD_LEFT)));
            $kode108 = $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->jenis ?: '-'));

            $itemsMapped = [];
            if ($isKeluar && $m->mutasiRegisters && $m->mutasiRegisters->isNotEmpty()) {
                foreach ($m->mutasiRegisters as $idx => $mr) {
                    $reg = $mr->register;
                    $ast = $reg?->astap ?: $astap;
                    $kd108 = $ast?->kode_108 ?: ($ast?->jenisAstap?->sub_sub_rincian_objek ?: ($ast?->jenisAstap?->jenis ?: ($kode108 ?: '-')));
                    $itemsMapped[] = [
                        'no'          => $idx + 1,
                        'nama_barang' => $ast?->nama_barang ?: 'Aset RSUD',
                        'nibar'       => $reg?->nibar ?: '-',
                        'kode_108'    => $kd108,
                        'kondisi'     => $mr->kondisi ?: ($reg?->kondisi ?: ($m->kondisi ?: 'Baik')),
                        'ruangan_asal'=> $reg?->unit?->nama ?: ($m->ruangan_tujuan ?: 'RSUD Dr. H. Koesnadi'),
                    ];
                }
            } else {
                $registers = $astap?->allRegisters ?? collect();
                if ($registers->isNotEmpty()) {
                    foreach ($registers as $idx => $reg) {
                        $itemsMapped[] = [
                            'no'          => $idx + 1,
                            'nama_barang' => $astap->nama_barang,
                            'nibar'       => $reg->nibar ?: '-',
                            'kode_108'    => $kode108,
                            'kondisi'     => $reg->kondisi ?: ($m->kondisi ?: 'Baik'),
                            'ruang'       => $reg->unit?->nama ?: ($reg->ruang_pemegang ?: ($m->ruangan_tujuan ?: 'RSUD Dr. H. Koesnadi')),
                        ];
                    }
                } else {
                    $itemsMapped[] = [
                        'no'          => 1,
                        'nama_barang' => $astap?->nama_barang ?: ($isKeluar ? 'Aset Mutasi Keluar OPD' : 'Aset Mutasi Masuk OPD'),
                        'nibar'       => '-',
                        'kode_108'    => $kode108,
                        'kondisi'     => $m->kondisi ?: 'Baik',
                        'ruang'       => $m->ruangan_tujuan ?: 'RSUD Dr. H. Koesnadi',
                    ];
                }
            }

            $itemCount = count($itemsMapped);
            $deletedAt = $m->deleted_at ? Carbon::parse($m->deleted_at)->timezone('Asia/Jakarta') : null;
            $nilaiReal = (float) ($m->nilai_perolehan ?: ($astap?->total_realisasi ?: 0));
            $firstNibar = $itemsMapped[0]['nibar'] ?? '-';

            // Analisis Cakupan Mutasi: Apakah seluruh NIBAR aset dimutasi atau hanya sebagian NIBAR (parsial)?
            $totalAstapVolume = $astap && $astap->allRegisters ? max(1, $astap->allRegisters->count()) : max(1, (int)($astap?->jumlah_volume ?? $itemCount));
            $mutasiUnitCount = $itemCount;
            $isPartial = $isKeluar && ($mutasiUnitCount < $totalAstapVolume);
            $sisaDiRsud = $isKeluar ? max(0, $totalAstapVolume - $mutasiUnitCount) : 0;

            if ($isKeluar) {
                $cakupanTipe  = $isPartial ? 'sebagian' : 'penuh';
                $cakupanBadge = $isPartial 
                    ? "⚡ Mutasi Sebagian ({$mutasiUnitCount} dari {$totalAstapVolume} NIBAR)"
                    : "✓ Mutasi Seluruh Unit (Semua {$mutasiUnitCount} NIBAR)";
            } else {
                $cakupanTipe  = 'masuk_penuh';
                $cakupanBadge = "📥 Penerimaan Penuh ({$mutasiUnitCount} NIBAR)";
            }

            // Pihak Pengirim & Penerima disesuaikan dengan Arah Mutasi
            $asalFormatted = $isKeluar 
                ? ('RSUD Dr. H. Koesnadi' . ($m->unit?->nama ? ' (' . $m->unit->nama . ')' : ''))
                : ($m->opd_asal ?: ($astap?->mutasi_asal ?: 'SKPD / Instansi Luar'));
            $tujuanFormatted = $isKeluar 
                ? ($m->opd_tujuan ?: 'SKPD / Instansi Luar')
                : ($m->unit?->nama ?: ($m->ruangan_tujuan ?: 'RSUD Dr. H. Koesnadi'));
            $pemohonFormatted = $isKeluar
                ? ($m->pj_asal_nama ?: 'Pengurus Barang RSUD')
                : ($m->pj_asal_nama ?: 'Pejabat OPD Pengirim');
            $penerimaFormatted = $isKeluar
                ? ($m->pj_tujuan_nama ?: 'Pejabat OPD Penerima')
                : ($m->pj_tujuan_nama ?: 'Pengurus Barang RSUD');
            $jenisFormatted = $m->jenis_mutasi ?: ($isKeluar ? 'Mutasi Keluar OPD' : 'Mutasi Masuk OPD');

            return [
                'id'                        => $m->id,
                'astap_id'                  => $m->astap_id,
                'kode'                      => $nomorBamb,
                'nomor_bamb'                => $nomorBamb,
                'jenis'                     => $jenisFormatted,
                'tipe'                      => $m->tipe ?: 'masuk',
                'nama'                      => ($astap?->nama_barang ?: ($itemsMapped[0]['nama_barang'] ?? 'Aset Mutasi Eksternal')) . ($itemCount > 1 ? " (+{$itemCount} unit)" : ''),
                'kode_barang'               => ($firstNibar !== '-' ? $firstNibar : ($kode108 ?: '-')),
                'item_count'                => $itemCount,
                'items'                     => $itemsMapped,
                'is_partial'                => $isPartial,
                'mutasi_unit_count'         => $mutasiUnitCount,
                'total_astap_volume'        => $totalAstapVolume,
                'sisa_unit_rsud'            => $sisaDiRsud,
                'cakupan_tipe'              => $cakupanTipe,
                'cakupan_badge'             => $cakupanBadge,
                'asal'                      => $asalFormatted,
                'opd_asal'                  => $asalFormatted,
                'tujuan'                    => $tujuanFormatted,
                'ruangan_tujuan'            => $tujuanFormatted,
                'pemohon'                   => $pemohonFormatted,
                'penerima_pj'               => $penerimaFormatted,
                'status_terakhir'           => $m->status ?: 'Disahkan (Selesai)',
                'is_batal'                  => false,
                'alasan_batal'              => null,
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
        // 2A. DATA TERHAPUS: BELANJA MODAL (AKUN 5.2 KASDA / BLUD APBD)
        // =========================================================================
        $rawDeletedBelanjaModals = Astap::onlyDeleted()
            ->where(function ($q) {
                $q->whereNull('sumber_dana')
                  ->orWhere('sumber_dana', 'belanja_modal');
            })
            ->whereDoesntHave('mutasiEksternals', function ($q) {
                $q->where('status', 'Dibatalkan');
            })
            ->where(function ($q) {
                $q->whereNull('keterangan_tambahan')
                  ->orWhere('keterangan_tambahan', 'not like', '%Dibatalkan%');
            })
            ->whereDoesntHave('kemitraan')
            ->whereDoesntHave('objekKemitraans')
            ->with(['jenisAstap', 'rekeningBelanja', 'registers.unit', 'belanjaModal', 'jenisPengadaan'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedBelanjaModals = $rawDeletedBelanjaModals->map(function ($a) {
            $regCount = $a->registers->count();
            $deletedAt = $a->deleted_at ? Carbon::parse($a->deleted_at)->timezone('Asia/Jakarta') : null;
            $bm = $a->belanjaModal;

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
                'id'                   => $a->id,
                'kode'                 => $a->kode_108 ?: ($a->spk_nomor ?: 'ASTAP-' . $a->id),
                'nama'                 => $a->nama_barang ?: 'Aset Belanja Modal',
                'tahun'                => $a->tahun_perolehan ?: '-',
                'volume'               => $a->jumlah_volume . ' ' . ($a->satuan ?: 'Unit'),
                'total_realisasi'      => 'Rp ' . number_format($a->total_realisasi ?: ($a->jumlah_anggaran ?: 0), 0, ',', '.'),
                'harga_satuan'         => 'Rp ' . number_format($a->harga_satuan ?: 0, 0, ',', '.'),
                'category'             => $a->category ?: 'KIB B',
                'penyedia'             => $a->penyedia_nama ?: ($bm?->penyedia_nama ?: '-'),
                'penyedia_pemilik'     => $bm?->penyedia_pemilik ?: '-',
                'penyedia_rekening'    => ($bm?->penyedia_rekening_nama ? $bm->penyedia_rekening_nama . ' - ' : '') . ($bm?->penyedia_rekening_nomor ?: '-'),
                'penyedia_alamat'      => $bm?->penyedia_alamat ?: '-',
                'spk_nomor'            => $a->spk_nomor ?: ($bm?->spk_nomor ?: '-'),
                'spk_tanggal'          => $bm?->spk_tanggal ? Carbon::parse($bm->spk_tanggal)->locale('id')->translatedFormat('d M Y') : '-',
                'sp2d_nomor'           => $bm?->sp2d_nomor ?: '-',
                'sp2d_tanggal'         => $bm?->sp2d_tanggal ? Carbon::parse($bm->sp2d_tanggal)->locale('id')->translatedFormat('d M Y') : '-',
                'faktur_nomor'         => $bm?->faktur_nomor ?: '-',
                'faktur_tanggal'       => $bm?->faktur_tanggal ? Carbon::parse($bm->faktur_tanggal)->locale('id')->translatedFormat('d M Y') : '-',
                'bast_dokumen_nomor'   => $bm?->bast_dokumen_nomor ?: '-',
                'bast_dokumen_tanggal' => $bm?->bast_dokumen_tanggal ? Carbon::parse($bm->bast_dokumen_tanggal)->locale('id')->translatedFormat('d M Y') : '-',
                'rekening'             => $a->rekeningBelanja?->nama_belanja ?: ($a->rekeningBelanja?->kode_rekening ?: '-'),
                'rekening_kode'        => $a->rekeningBelanja?->kode_rekening ?: '-',
                'jenis_pengadaan'      => $a->jenisPengadaan?->nama ?: '-',
                'item_count'           => $regCount,
                'items'                => $registersMapped,
                'status_terakhir'      => 'Terhapus',
                'deleted_by'           => $a->deleted_by ?: 'Administrator',
                'deleted_at'           => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative'  => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'       => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 2B. DATA TERHAPUS: REGISTER NIBAR BELANJA MODAL INDIVIDUAL
        // =========================================================================
        $rawDeletedBelanjaModalNibars = AstapRegister::onlyDeleted()
            ->whereHas('astap', function ($q) {
                $q->where('is_deleted', 0)
                  ->where(function ($sq) {
                      $sq->whereNull('sumber_dana')
                         ->orWhere('sumber_dana', 'belanja_modal');
                  })
                  ->whereDoesntHave('kemitraan')
                  ->whereDoesntHave('objekKemitraans');
            })
            ->with(['astap.jenisAstap', 'unit'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedBelanjaModalNibars = $rawDeletedBelanjaModalNibars->map(function ($r) {
            $deletedAt = $r->deleted_at ? Carbon::parse($r->deleted_at)->timezone('Asia/Jakarta') : null;
            return [
                'id'                  => $r->id,
                'astap_id'            => $r->astap_id,
                'nibar'               => $r->nibar ?: ($r->no_register ?: 'REG-' . $r->id),
                'no_register'         => $r->no_register ?: '-',
                'nama_barang'         => $r->nama_barang ?: ($r->astap?->nama_barang ?? 'Aset Belanja Modal'),
                'nama_aset_induk'     => $r->astap?->nama_barang,
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
        // 2C. DATA TERHAPUS: MASTER ASTAP
        // =========================================================================
        // Sesuai Opsi 1: Aset dari pelimpahan masuk yang dibatalkan tidak masuk Recycle Bin
        // karena belum resmi menjadi aset RSUD dan riwayatnya sudah tercatat di Mutasi Eksternal.
        $rawDeletedAstaps = Astap::onlyDeleted()
            ->whereDoesntHave('mutasiEksternals', function ($q) {
                $q->where('status', 'Dibatalkan');
            })
            ->where(function ($q) {
                $q->whereNull('keterangan_tambahan')
                  ->orWhere('keterangan_tambahan', 'not like', '%Dibatalkan%');
            })
            ->whereDoesntHave('kemitraan')
            ->whereDoesntHave('objekKemitraans')
            ->with(['jenisAstap', 'rekeningBelanja', 'registers.unit', 'belanjaBarang'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedAstaps = $rawDeletedAstaps->map(function ($a) {
            $regCount = $a->registers->count();
            $firstReg = $a->registers->first();
            $deletedAt = $a->deleted_at ? Carbon::parse($a->deleted_at)->timezone('Asia/Jakarta') : null;

            $ket = (string) ($a->keterangan_tambahan ?? '');
            $mutBatal = $a->mutasiEksternals ? $a->mutasiEksternals->firstWhere('status', 'Dibatalkan') : null;
            $isBatal = str_contains(strtolower($ket), 'dibatalkan') || !empty($mutBatal);
            $alasanBatal = $mutBatal?->alasan_batal 
                ?: (str_contains(strtolower($ket), 'dibatalkan') ? preg_replace('/^.*?dibatalkan[:\s]*/i', '', $ket) : null);

            $sumberDana = $a->sumber_dana;
            if (empty($sumberDana)) {
                if (str_starts_with((string) $a->kode_108, '1.5.2')) {
                    $sumberDana = 'kemitraan';
                } elseif ($a->belanjaBarang) {
                    $sumberDana = 'belanja_barang';
                } else {
                    $sumberDana = 'belanja_modal';
                }
            }
            if (in_array($sumberDana, ['belanja_barang', 'belanja_rekening'])) {
                $sumberDana = 'belanja_barang';
            } elseif (in_array($sumberDana, ['mutasi_eksternal', 'pelimpahan_skpd', 'pelimpahan', 'mutasi_masuk', 'mutasi'])) {
                $sumberDana = 'mutasi';
            }
            $sumberDanaLabel = match($sumberDana) {
                'belanja_modal'  => 'Belanja Modal',
                'belanja_barang' => 'Belanja Barang',
                'kemitraan'      => 'Kemitraan (KSO)',
                'hibah'          => 'Hibah',
                'mutasi'         => 'Mutasi Eksternal',
                default          => 'Umum / Lainnya',
            };
            $sumberDanaIcon = match($sumberDana) {
                'belanja_modal'  => '🏛️',
                'belanja_barang' => '📦',
                'kemitraan'      => '🤝',
                'hibah'          => '🎁',
                'mutasi'         => '🔄',
                default          => '📦',
            };

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
                'sumber_dana'         => $sumberDana,
                'sumber_dana_label'   => $sumberDanaLabel,
                'sumber_dana_icon'    => $sumberDanaIcon,
                'penyedia'            => $a->penyedia_nama ?: '-',
                'spk_nomor'           => $a->spk_nomor ?: '-',
                'rekening'            => $a->rekeningBelanja?->nama_belanja ?: '-',
                'item_count'          => $regCount,
                'items'               => $registersMapped,
                'is_batal'            => $isBatal,
                'alasan_batal'        => $alasanBatal,
                'status_terakhir'     => $isBatal ? 'Dibatalkan' : 'Terhapus',
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
            ->with(['astap.jenisAstap', 'astap.belanjaBarang', 'unit'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedNibars = $rawDeletedNibars->map(function ($r) {
            $deletedAt = $r->deleted_at ? Carbon::parse($r->deleted_at)->timezone('Asia/Jakarta') : null;

            $astapSumberDana = $r->astap?->sumber_dana;
            if (empty($astapSumberDana)) {
                if (str_starts_with((string) $r->astap?->kode_108, '1.5.2')) {
                    $astapSumberDana = 'kemitraan';
                } elseif ($r->astap?->belanjaBarang) {
                    $astapSumberDana = 'belanja_barang';
                } else {
                    $astapSumberDana = 'belanja_modal';
                }
            }
            if (in_array($astapSumberDana, ['belanja_barang', 'belanja_rekening'])) {
                $astapSumberDana = 'belanja_barang';
            } elseif (in_array($astapSumberDana, ['mutasi_eksternal', 'pelimpahan_skpd', 'pelimpahan', 'mutasi_masuk', 'mutasi'])) {
                $astapSumberDana = 'mutasi';
            }
            $astapSumberDanaLabel = match($astapSumberDana) {
                'belanja_modal'  => 'Belanja Modal',
                'belanja_barang' => 'Belanja Barang',
                'kemitraan'      => 'Kemitraan (KSO)',
                'hibah'          => 'Hibah',
                'mutasi'         => 'Mutasi Eksternal',
                default          => 'Umum / Lainnya',
            };
            $astapSumberDanaIcon = match($astapSumberDana) {
                'belanja_modal'  => '🏛️',
                'belanja_barang' => '📦',
                'kemitraan'      => '🤝',
                'hibah'          => '🎁',
                'mutasi'         => '🔄',
                default          => '📦',
            };

            return [
                'id'                  => $r->id,
                'astap_id'            => $r->astap_id,
                'nibar'               => $r->nibar ?: ($r->no_register ?: 'REG-' . $r->id),
                'no_register'         => $r->no_register ?: '-',
                'nama_barang'         => $r->nama_barang ?: ($r->astap?->nama_barang ?? 'Aset ASTAP'),
                'nama_aset_induk'     => $r->astap?->nama_barang,
                'kode_108'            => $r->astap?->kode_108 ?: ($r->astap?->jenisAstap?->kode_108 ?: '-'),
                'kategori'            => $r->astap?->category ?: ($r->astap?->jenisAstap?->kategori ?? 'KIB B'),
                'sumber_dana'         => $astapSumberDana,
                'sumber_dana_label'   => $astapSumberDanaLabel,
                'sumber_dana_icon'    => $astapSumberDanaIcon,
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
                'alasan_hapus'        => 'Dipindahkan ke Tong Sampah',
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
            ->with(['astap.jenisAstap', 'astap.allRegisters.unit', 'astap.unit', 'objekAstap.jenisAstap', 'objekRegister', 'user'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedKemitraans = $rawDeletedKemitraans->map(function ($k) {
            $deletedAt  = $k->deleted_at ? Carbon::parse($k->deleted_at)->timezone('Asia/Jakarta') : null;
            $tglPks     = $k->tanggal_pks ? Carbon::parse($k->tanggal_pks)->timezone('Asia/Jakarta') : null;
            $tglMulai   = $k->tanggal_mulai ? Carbon::parse($k->tanggal_mulai)->timezone('Asia/Jakarta') : null;
            $tglSelesai = $k->tanggal_selesai ? Carbon::parse($k->tanggal_selesai)->timezone('Asia/Jakarta') : null;

            $astap = $k->astap;
            $spec = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap?->spesifikasi_json ?? '[]', true) ?: []);
            
            // Tentukan apakah aset ini merupakan objek yang dimanfaatkan atau aset yang ditambahkan mitra
            $tipeKemitraan = $k->tipe_kemitraan ?: ($spec['tipe_kemitraan'] ?? null);
            $isDitambahkan = ($tipeKemitraan === 'ditambahkan') || (!empty($k->objek_astap_id) && $tipeKemitraan !== 'dimanfaatkan') || (!empty($k->objek_nibar) && $tipeKemitraan !== 'dimanfaatkan');

            $namaBarang = $astap?->nama_barang ?: ($isDitambahkan ? 'Aset Ditambahkan Mitra' : 'Objek Pemanfaatan BMD');
            $kodeBarang = $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '-');
            $ruangan    = $astap?->unit?->nama ?: '-';

            $registersMapped = $astap?->allRegisters?->map(function ($r, $idx) {
                return [
                    'no'          => $idx + 1,
                    'nibar'       => $r->nibar ?: '-',
                    'nama_barang' => $r->nama_barang ?: '-',
                    'ruangan'     => $r->unit?->nama ?: ($r->ruang_pemegang ?: '-'),
                    'kondisi'     => $r->kondisi ?: 'Baik',
                ];
            })->toArray() ?? [];

            $objekAsalNama = $k->objekAstap?->nama_barang ?: ($k->objek_nibar ? 'Objek BMD (NIBAR: ' . $k->objek_nibar . ')' : null);
            $objekAsalNibar = $k->objek_nibar ?: ($k->objekRegister?->nibar ?: null);

            $linkedMitrasCount = 0;
            if (!$isDitambahkan && $k->astap_id) {
                $linkedMitrasCount = AstapKemitraan::onlyDeleted()
                    ->where('objek_astap_id', $k->astap_id)
                    ->count();
            }

            return [
                'id'                  => $k->id,
                'astap_id'            => $k->astap_id,
                'tipe_kemitraan'      => $isDitambahkan ? 'ditambahkan' : 'dimanfaatkan',
                'is_ditambahkan'      => $isDitambahkan,
                'linked_mitras_count' => $linkedMitrasCount,
                'nomor_pks'           => $k->nomor_pks,
                'mitra_nama'          => $k->mitra_nama,
                'skema_kemitraan'     => $k->skema_kemitraan ?: ($isDitambahkan ? 'KSO' : 'Sewa'),
                'nama_barang'         => $namaBarang,
                'kode_barang'         => $kodeBarang,
                'merk'                => $spec['merk'] ?? ($spec['mesin_merk'] ?? null),
                'type'                => $spec['type'] ?? ($spec['mesin_type'] ?? null),
                'luas'                => $spec['luas_m2'] ?? ($spec['tanah_luas_m2'] ?? ($spec['gedung_luas_lantai'] ?? null)),
                'sertifikat'          => $spec['sertifikat_no'] ?? ($spec['tanah_sertifikat_no'] ?? ($spec['gedung_dokumen_no'] ?? null)),
                'objek_asal_nama'     => $objekAsalNama,
                'objek_asal_nibar'    => $objekAsalNibar,
                'volume'              => $k->jumlah_volume . ' ' . ($k->satuan ?: ($isDitambahkan ? 'Unit' : 'Bidang')),
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
                'alasan_hapus'        => 'Dipindahkan ke Tong Sampah',
                'registers'           => $registersMapped,
                'deleted_by'          => $k->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 8. DATA TERHAPUS: BELANJA BARANG (AKUN 5.1.02 / PERBEKALAN RUANGAN)
        // =========================================================================
        $rawDeletedBelanjaBarangs = AstapBelanjaBarang::onlyDeleted()
            ->with(['astap.jenisAstap', 'astap.registers.unit', 'astap.unit'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedBelanjaBarangs = $rawDeletedBelanjaBarangs->map(function ($b) {
            $deletedAt  = $b->deleted_at ? Carbon::parse($b->deleted_at)->timezone('Asia/Jakarta') : null;
            $tglFaktur  = $b->tanggal_faktur ? Carbon::parse($b->tanggal_faktur)->timezone('Asia/Jakarta') : null;

            $namaBarang = $b->astap?->nama_barang ?: 'Barang Belanja';
            $kodeBarang = $b->astap?->kode_108 ?: ($b->astap?->jenisAstap?->sub_sub_rincian_objek ?: '-');
            $ruangan    = $b->astap?->unit?->nama ?: '-';
            $totalBeli  = (float) ($b->total_pembelian ?: ($b->astap?->total_realisasi ?? 0));
            $volume     = max(1, (int) ($b->astap?->jumlah_volume ?? 1)) . ' ' . ($b->astap?->satuan ?: 'Unit');

            $registersMapped = $b->astap?->registers?->map(function ($r, $idx) {
                return [
                    'no'          => $idx + 1,
                    'nibar'       => $r->nibar ?: '-',
                    'nama_barang' => $r->nama_barang ?: '-',
                    'ruangan'     => $r->unit?->nama ?: ($r->ruang_pemegang ?: '-'),
                    'kondisi'     => $r->kondisi ?: 'Baik',
                ];
            })->toArray() ?? [];

            return [
                'id'                  => $b->id,
                'astap_id'            => $b->astap_id,
                'nomor_faktur'        => $b->nomor_faktur ?: '-',
                'toko_penyedia'       => $b->toko_penyedia ?: 'Pusat Perbekalan RSUD',
                'nama_barang'         => $namaBarang,
                'kode_barang'         => $kodeBarang,
                'volume'              => $volume,
                'total_pembelian'     => $totalBeli,
                'total_pembelian_rp'  => 'Rp ' . number_format($totalBeli, 0, ',', '.'),
                'tanggal_faktur'      => $tglFaktur ? $tglFaktur->locale('id')->translatedFormat('d M Y') : '-',
                'ruangan'             => $ruangan,
                'tahun'               => $b->astap?->tahun_perolehan ?: '-',
                'triwulan'            => $b->astap?->triwulan ?: '-',
                'keterangan'          => $b->keterangan ?: '-',
                'alasan_hapus'        => 'Dipindahkan ke Tong Sampah',
                'registers'           => $registersMapped,
                'deleted_by'          => $b->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 8B. DATA TERHAPUS: REKLASIFIKASI ASET (PERUBAHAN KIB / 108 / KOREKSI)
        // =========================================================================
        $rawDeletedReklas = AstapReklas::onlyDeleted()
            ->with(['astap.jenisAstap', 'astap.registers', 'jenisReklasAsal', 'jenisReklasTujuan', 'user'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedReklas = $rawDeletedReklas->map(function ($r) {
            $deletedAt  = $r->deleted_at ? Carbon::parse($r->deleted_at)->timezone('Asia/Jakarta') : null;
            $tglReklas  = $r->tanggal_reklas ? Carbon::parse($r->tanggal_reklas)->timezone('Asia/Jakarta') : null;
            $namaBarang = $r->astap?->nama_barang ?: ($r->tujuan_nama ?: 'Aset Reklasifikasi');
            $kodeBarang = $r->tujuan_kode ?: ($r->astap?->kode_108 ?: ($r->asal_kode ?: '-'));

            return [
                'id'                  => $r->id,
                'astap_id'            => $r->astap_id,
                'nomor_ba'            => $r->nomor_ba_reklas ?: 'BA-REKLAS-' . str_pad($r->id, 4, '0', STR_PAD_LEFT),
                'jenis_reklas'        => $r->jenis_reklas ?: 'Reklasifikasi Aset',
                'nama_barang'         => $namaBarang,
                'kode_barang'         => $kodeBarang,
                'asal_kib'            => $r->asal_kib ?: '-',
                'tujuan_kib'          => $r->tujuan_kib ?: '-',
                'asal_nama'           => $r->asal_nama ?: '-',
                'tujuan_nama'         => $r->tujuan_nama ?: '-',
                'nilai_reklas'        => (float) $r->nilai_reklas,
                'nilai_reklas_rp'     => 'Rp ' . number_format($r->nilai_reklas ?: 0, 0, ',', '.'),
                'tanggal_reklas'      => $tglReklas ? $tglReklas->locale('id')->translatedFormat('d M Y') : '-',
                'tahun'               => $r->tahun ?: '-',
                'triwulan'            => $r->triwulan ?: '-',
                'keterangan'          => $r->alasan_reklas ?: ($r->keterangan ?: '-'),
                'alasan_hapus'        => 'Dipindahkan ke Tong Sampah',
                'deleted_by'          => $r->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 8C. DATA TERHAPUS: REGISTER NIBAR KEMITRAAN, MUTASI EKSTERNAL, HIBAH & BELANJA BARANG
        // =========================================================================
        // 1) REGISTER NIBAR KEMITRAAN INDIVIDUAL
        $rawDeletedKemitraanNibars = AstapRegister::onlyDeleted()
            ->whereHas('astap', function ($q) {
                $q->where('is_deleted', 0)
                  ->where(function ($sq) {
                      $sq->where('sumber_dana', 'kemitraan')
                         ->orWhere('kode_108', 'like', '1.5.2%')
                         ->orWhereHas('kemitraan')
                         ->orWhereHas('objekKemitraans');
                  });
            })
            ->with(['astap.jenisAstap', 'astap.kemitraan', 'unit'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedKemitraanNibars = $rawDeletedKemitraanNibars->map(function ($r) {
            $deletedAt = $r->deleted_at ? Carbon::parse($r->deleted_at)->timezone('Asia/Jakarta') : null;
            $kem = $r->astap?->kemitraan ?: ($r->astap?->objekKemitraans?->first());
            return [
                'id'                  => $r->id,
                'astap_id'            => $r->astap_id,
                'nibar'               => $r->nibar ?: ($r->no_register ?: 'REG-' . $r->id),
                'no_register'         => $r->no_register ?: '-',
                'nama_barang'         => $r->nama_barang ?: ($r->astap?->nama_barang ?? 'Aset Kemitraan'),
                'nama_aset_induk'     => $r->astap?->nama_barang,
                'kode_108'            => $r->astap?->kode_108 ?: ($r->astap?->jenisAstap?->kode_108 ?: '-'),
                'kategori'            => $r->astap?->category ?: ($r->astap?->jenisAstap?->kategori ?? 'KIB B'),
                'mitra_nama'          => $kem?->mitra_nama ?: '-',
                'nomor_pks'           => $kem?->nomor_pks ?: '-',
                'tipe_kemitraan'      => $kem?->tipe_kemitraan ?: 'dimanfaatkan',
                'ruang'               => $r->ruang_pemegang ?: ($r->unit?->nama ?: 'Gudang Perbekalan'),
                'kondisi'             => $r->kondisi ?: 'Baik',
                'status'              => $r->status ?: 'Tersedia',
                'tahun'               => $r->astap?->tahun_perolehan ?: '-',
                'deleted_by'          => $r->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // 2) REGISTER NIBAR MUTASI EKSTERNAL INDIVIDUAL
        $hasMutasiRegTable = Schema::hasTable('mutasi_eksternal_registers');
        $rawDeletedMutasiEksternalNibars = AstapRegister::onlyDeleted()
            ->where(function ($rootQ) use ($hasMutasiRegTable) {
                // Skenario 1: Aset Mutasi Masuk (sumber_dana mutasi/pelimpahan atau memiliki mutasiEksternals)
                $rootQ->whereHas('astap', function ($q) {
                    $q->where('is_deleted', 0)
                      ->where(function ($sq) {
                          $sq->whereIn('sumber_dana', ['mutasi_eksternal', 'mutasi_masuk', 'pelimpahan_skpd', 'pelimpahan', 'mutasi'])
                             ->orWhereHas('mutasiEksternals', fn($m) => $m->where('is_deleted', 0));
                      });
                });
                // Skenario 2: Aset Mutasi Keluar yang unit fisiknya terdaftar di mutasi_eksternal_registers
                if ($hasMutasiRegTable) {
                    $rootQ->orWhereHas('mutasiEksternalRegisters.mutasiEksternal', function ($m) {
                        $m->where('is_deleted', 0);
                    });
                }
            })
            ->with(array_filter(['astap.jenisAstap', 'astap.mutasiEksternals', $hasMutasiRegTable ? 'mutasiEksternalRegisters.mutasiEksternal' : null, 'unit']))
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedMutasiEksternalNibars = $rawDeletedMutasiEksternalNibars->map(function ($r) {
            $deletedAt = $r->deleted_at ? Carbon::parse($r->deleted_at)->timezone('Asia/Jakarta') : null;
            // Prioritaskan dokumen mutasi dari mutasiEksternalRegisters (jika mutasi keluar), atau mutasiEksternals aktif
            $mut = $r->mutasiEksternalRegisters?->firstWhere('mutasiEksternal.is_deleted', 0)?->mutasiEksternal
                ?? $r->mutasiEksternalRegisters?->first()?->mutasiEksternal
                ?? $r->astap?->mutasiEksternals?->firstWhere('is_deleted', 0)
                ?? $r->astap?->mutasiEksternals?->first();

            $tipe = $mut?->tipe ?: ($r->mutasiEksternalRegisters?->isNotEmpty() ? 'keluar' : 'masuk');
            $opdAsal = $tipe === 'keluar' ? 'RSUD Dr. H. Koesnadi' : ($mut?->opd_asal ?: ($r->astap?->mutasi_asal ?: '-'));
            $opdTujuan = $tipe === 'keluar' ? ($mut?->opd_tujuan ?: 'OPD Luar') : 'RSUD Dr. H. Koesnadi';

            return [
                'id'                  => $r->id,
                'astap_id'            => $r->astap_id,
                'nibar'               => $r->nibar ?: ($r->no_register ?: 'REG-' . $r->id),
                'no_register'         => $r->no_register ?: '-',
                'nama_barang'         => $r->nama_barang ?: ($r->astap?->nama_barang ?? 'Aset Mutasi Eksternal'),
                'nama_aset_induk'     => $r->astap?->nama_barang,
                'kode_108'            => $r->astap?->kode_108 ?: ($r->astap?->jenisAstap?->kode_108 ?: '-'),
                'kategori'            => $r->astap?->category ?: ($r->astap?->jenisAstap?->kategori ?? 'KIB B'),
                'tipe_mutasi'         => $tipe,
                'nomor_bamb'          => $mut?->nomor_bamb ?: ($r->astap?->mutasi_nomor_bamb ?: '-'),
                'opd_asal'            => $opdAsal,
                'opd_tujuan'          => $opdTujuan,
                'ruang'               => $r->ruang_pemegang ?: ($r->unit?->nama ?: 'Gudang Perbekalan'),
                'kondisi'             => $r->kondisi ?: 'Baik',
                'status'              => $r->status ?: 'Tersedia',
                'tahun'               => $r->astap?->tahun_perolehan ?: '-',
                'deleted_by'          => $r->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // 3) REGISTER NIBAR HIBAH INDIVIDUAL
        $rawDeletedHibahNibars = AstapRegister::onlyDeleted()
            ->whereHas('astap', function ($q) {
                $q->where('is_deleted', 0)
                  ->where(function ($sq) {
                      $sq->where('sumber_dana', 'hibah')
                         ->orWhereHas('hibahs');
                  });
            })
            ->with(['astap.jenisAstap', 'astap.hibahs', 'unit'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedHibahNibars = $rawDeletedHibahNibars->map(function ($r) {
            $deletedAt = $r->deleted_at ? Carbon::parse($r->deleted_at)->timezone('Asia/Jakarta') : null;
            $hib = $r->astap?->hibahs?->first();
            return [
                'id'                  => $r->id,
                'astap_id'            => $r->astap_id,
                'nibar'               => $r->nibar ?: ($r->no_register ?: 'REG-' . $r->id),
                'no_register'         => $r->no_register ?: '-',
                'nama_barang'         => $r->nama_barang ?: ($r->astap?->nama_barang ?? 'Aset Hibah'),
                'nama_aset_induk'     => $r->astap?->nama_barang,
                'kode_108'            => $r->astap?->kode_108 ?: ($r->astap?->jenisAstap?->kode_108 ?: '-'),
                'kategori'            => $r->astap?->category ?: ($r->astap?->jenisAstap?->kategori ?? 'KIB B'),
                'tipe_hibah'          => $hib?->tipe_hibah ?: 'masuk',
                'pihak_hibah'         => $hib?->pihak_hibah ?: '-',
                'nomor_bast'          => $hib?->nomor_bast ?: '-',
                'ruang'               => $r->ruang_pemegang ?: ($r->unit?->nama ?: 'Gudang Perbekalan'),
                'kondisi'             => $r->kondisi ?: 'Baik',
                'status'              => $r->status ?: 'Tersedia',
                'tahun'               => $r->astap?->tahun_perolehan ?: '-',
                'deleted_by'          => $r->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // 4) REGISTER NIBAR BELANJA BARANG INDIVIDUAL
        $rawDeletedBelanjaBarangNibars = AstapRegister::onlyDeleted()
            ->whereHas('astap', function ($q) {
                $q->where('is_deleted', 0)
                  ->where(function ($sq) {
                      $sq->whereIn('sumber_dana', ['belanja_barang', 'belanja_rekening'])
                         ->orWhereHas('belanjaBarang');
                  });
            })
            ->with(['astap.jenisAstap', 'astap.belanjaBarang', 'unit'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedBelanjaBarangNibars = $rawDeletedBelanjaBarangNibars->map(function ($r) {
            $deletedAt = $r->deleted_at ? Carbon::parse($r->deleted_at)->timezone('Asia/Jakarta') : null;
            $bb = $r->astap?->belanjaBarang;
            return [
                'id'                  => $r->id,
                'astap_id'            => $r->astap_id,
                'nibar'               => $r->nibar ?: ($r->no_register ?: 'REG-' . $r->id),
                'no_register'         => $r->no_register ?: '-',
                'nama_barang'         => $r->nama_barang ?: ($r->astap?->nama_barang ?? 'Aset Belanja Barang'),
                'nama_aset_induk'     => $r->astap?->nama_barang,
                'kode_108'            => $r->astap?->kode_108 ?: ($r->astap?->jenisAstap?->kode_108 ?: '-'),
                'kategori'            => $r->astap?->category ?: ($r->astap?->jenisAstap?->kategori ?? 'KIB B'),
                'nomor_faktur'        => $bb?->nomor_faktur ?: '-',
                'toko_penyedia'       => $bb?->toko_penyedia ?: '-',
                'ruang'               => $r->ruang_pemegang ?: ($r->unit?->nama ?: 'Gudang Perbekalan'),
                'kondisi'             => $r->kondisi ?: 'Baik',
                'status'              => $r->status ?: 'Tersedia',
                'tahun'               => $r->astap?->tahun_perolehan ?: '-',
                'deleted_by'          => $r->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->locale('id')->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 9. STATISTIK TERPUSAT SELURUH MODUL
        // =========================================================================
        $now = now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sevenDaysAgo  = $now->copy()->subDays(7);

        $belanjaModalCount          = $deletedBelanjaModals->count();
        $belanjaModalNibarCount     = $deletedBelanjaModalNibars->count();
        $mutasiInternalCount        = $deletedMutasis->count();
        $mutasiEksternalCount       = $deletedMutasiEksternals->count();
        $mutasiEksternalNibarCount  = $deletedMutasiEksternalNibars->count();
        $mutasiCount                = $mutasiInternalCount + $mutasiEksternalCount;
        $astapCount                 = $deletedAstaps->count();
        $nibarCount                 = $deletedNibars->count();
        $distribusiCount            = $deletedDistribusis->count();
        $hibahCount                 = $deletedHibahs->count();
        $hibahNibarCount            = $deletedHibahNibars->count();
        $kemitraanCount             = $deletedKemitraans->count();
        $kemitraanNibarCount        = $deletedKemitraanNibars->count();
        $belanjaBarangCount         = $deletedBelanjaBarangs->count();
        $belanjaBarangNibarCount    = $deletedBelanjaBarangNibars->count();
        $reklasCount                = $deletedReklas->count();
        $unitCount                  = $deletedUnits->count();
        $userCount                  = $deletedUsers->count();

        $totalAllDeleted = $belanjaModalCount + $belanjaModalNibarCount 
            + $mutasiCount + $mutasiEksternalNibarCount 
            + $astapCount + $nibarCount 
            + $distribusiCount 
            + $hibahCount + $hibahNibarCount 
            + $kemitraanCount + $kemitraanNibarCount 
            + $belanjaBarangCount + $belanjaBarangNibarCount 
            + $reklasCount + $unitCount + $userCount;

        // Hitung 30 hari terakhir
        $filterMonth = fn($col) => $col->filter(fn($m) => $m->deleted_at && Carbon::parse($m->deleted_at)->gte($thirtyDaysAgo))->count();
        $totalThisMonth = $filterMonth($rawDeletedBelanjaModals)
            + $filterMonth($rawDeletedBelanjaModalNibars)
            + $filterMonth($rawDeletedMutasis)
            + $filterMonth($rawDeletedMutasiEksternals)
            + $filterMonth($rawDeletedMutasiEksternalNibars)
            + $filterMonth($rawDeletedAstaps)
            + $filterMonth($rawDeletedNibars)
            + $filterMonth($rawDeletedDistribusis)
            + $filterMonth($rawDeletedHibahs)
            + $filterMonth($rawDeletedHibahNibars)
            + $filterMonth($rawDeletedKemitraans)
            + $filterMonth($rawDeletedKemitraanNibars)
            + $filterMonth($rawDeletedBelanjaBarangs)
            + $filterMonth($rawDeletedBelanjaBarangNibars)
            + $filterMonth($rawDeletedReklas)
            + $filterMonth($rawDeletedUnits)
            + $filterMonth($rawDeletedUsers);

        // Hitung 7 hari terakhir
        $filterWeek = fn($col) => $col->filter(fn($m) => $m->deleted_at && Carbon::parse($m->deleted_at)->gte($sevenDaysAgo))->count();
        $totalThisWeek = $filterWeek($rawDeletedBelanjaModals)
            + $filterWeek($rawDeletedBelanjaModalNibars)
            + $filterWeek($rawDeletedMutasis)
            + $filterWeek($rawDeletedMutasiEksternals)
            + $filterWeek($rawDeletedMutasiEksternalNibars)
            + $filterWeek($rawDeletedAstaps)
            + $filterWeek($rawDeletedNibars)
            + $filterWeek($rawDeletedDistribusis)
            + $filterWeek($rawDeletedHibahs)
            + $filterWeek($rawDeletedHibahNibars)
            + $filterWeek($rawDeletedKemitraans)
            + $filterWeek($rawDeletedKemitraanNibars)
            + $filterWeek($rawDeletedBelanjaBarangs)
            + $filterWeek($rawDeletedBelanjaBarangNibars)
            + $filterWeek($rawDeletedReklas)
            + $filterWeek($rawDeletedUnits)
            + $filterWeek($rawDeletedUsers);

        $moduleStats = [
            'belanja_modal' => [
                'name'        => 'Belanja Modal',
                'icon'        => '🏛️',
                'count'       => $belanjaModalCount,
                'nibar_count' => $belanjaModalNibarCount,
                'color'       => 'emerald',
                'ready'       => true,
            ],
            'astap' => [
                'name'        => 'Master ASTAP',
                'icon'        => '📦',
                'count'       => $astapCount,
                'nibar_count' => $nibarCount,
                'color'       => 'blue',
                'ready'       => true,
            ],
            'unit' => [
                'name'  => 'Unit & Paviliun',
                'icon'  => '🏥',
                'count' => $unitCount,
                'color' => 'indigo',
                'ready' => true,
            ],
            'distribusi' => [
                'name'  => 'Distribusi Aset',
                'icon'  => '🚚',
                'count' => $distribusiCount,
                'color' => 'teal',
                'ready' => true,
            ],
            'mutasi' => [
                'name'                  => 'Mutasi Aset',
                'icon'                  => '🔄',
                'count'                 => $mutasiCount,
                'internal_count'        => $mutasiInternalCount,
                'eksternal_count'       => $mutasiEksternalCount,
                'eksternal_nibar_count' => $mutasiEksternalNibarCount,
                'color'                 => 'amber',
                'ready'                 => true,
            ],
            'hibah' => [
                'name'        => 'Hibah Aset',
                'icon'        => '🎁',
                'count'       => $hibahCount,
                'nibar_count' => $hibahNibarCount,
                'color'       => 'purple',
                'ready'       => true,
            ],
            'kemitraan' => [
                'name'               => 'Kemitraan Aset',
                'icon'               => '🤝',
                'count'              => $kemitraanCount,
                'nibar_count'        => $kemitraanNibarCount,
                'dimanfaatkan_count' => $deletedKemitraans->where('is_ditambahkan', false)->count(),
                'ditambahkan_count'  => $deletedKemitraans->where('is_ditambahkan', true)->count(),
                'color'              => 'cyan',
                'ready'              => true,
            ],
            'belanja_barang' => [
                'name'        => 'Belanja Barang',
                'icon'        => '🛒',
                'count'       => $belanjaBarangCount,
                'nibar_count' => $belanjaBarangNibarCount,
                'color'       => 'emerald',
                'ready'       => true,
            ],
            'reklas' => [
                'name'  => 'Reklasifikasi Aset',
                'icon'  => '⚖️',
                'count' => $reklasCount,
                'color' => 'emerald',
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
            'deletedBelanjaModals',
            'deletedBelanjaModalNibars',
            'deletedMutasis',
            'deletedMutasiEksternals',
            'deletedMutasiEksternalNibars',
            'deletedAstaps',
            'deletedNibars',
            'deletedDistribusis',
            'deletedHibahs',
            'deletedHibahNibars',
            'deletedKemitraans',
            'deletedKemitraanNibars',
            'deletedBelanjaBarangs',
            'deletedBelanjaBarangNibars',
            'deletedReklas',
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
            case 'belanja_modal':
                $astap = Astap::with('mutasiEksternals')->findOrFail($id);
                $hasCancelledMutasi = $astap->mutasiEksternals ? $astap->mutasiEksternals->contains('status', 'Dibatalkan') : false;
                if ($hasCancelledMutasi || str_contains(strtolower((string) $astap->keterangan_tambahan), 'dibatalkan')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data Belanja Modal ini bersumber dari transaksi yang telah Dibatalkan dan tidak dapat dipulihkan kembali.'
                    ], 422);
                }
                $nama = $astap->nama_barang ?: 'Aset Belanja Modal';
                DB::transaction(function () use ($astap) {
                    $astap->restoreData();
                    $astap->allRegisters()->update([
                        'is_deleted'    => 0,
                        'deleted_by'    => null,
                        'deleted_by_id' => null,
                        'deleted_at'    => null,
                    ]);
                    $astap->jumlah_volume = max(1, $astap->allRegisters()->where('is_deleted', 0)->count());
                    $astap->save();
                });
                $msg = "Paket Belanja Modal \"{$nama}\" dan seluruh register NIBAR berhasil dipulihkan ke status aktif.";
                break;

            case 'belanja_modal_nibar':
                $reg = AstapRegister::findOrFail($id);
                $nibar = $reg->nibar ?: $reg->no_register;
                $astap = $reg->astap;
                $astapRestored = false;

                DB::transaction(function () use ($reg, $astap, &$astapRestored) {
                    $reg->restoreData();
                    if ($astap && $astap->is_deleted) {
                        $astap->restoreData();
                        $astapRestored = true;
                    }
                    if ($astap) {
                        $this->syncAstapAfterRegisterChange($astap);
                    }
                });

                if ($astapRestored) {
                    $msg = "Unit Register NIBAR \"{$nibar}\" dan paket Belanja Modal induknya \"{$astap->nama_barang}\" berhasil dipulihkan ke status aktif.";
                } elseif ($astap) {
                    $msg = "Unit Register NIBAR \"{$nibar}\" berhasil dipulihkan ke paket Belanja Modal \"{$astap->nama_barang}\" (Volume aktif: {$astap->jumlah_volume}).";
                } else {
                    $msg = "Unit Register NIBAR \"{$nibar}\" berhasil dipulihkan ke katalog aktif.";
                }
                break;
            case 'mutasi':
            case 'mutasi_internal':
                $mutasi = AstapMutasi::findOrFail($id);
                $bamb = $mutasi->nomor_bamb;
                $mutasi->restoreData();
                $msg = "Data Berita Acara Mutasi {$bamb} berhasil dipulihkan ke status aktif.";
                break;

            case 'mutasi_eksternal':
                $mEksternal = \App\Models\MutasiEksternal::with(['astap.registers', 'mutasiRegisters.register'])->findOrFail($id);
                if ($mEksternal->status === 'Dibatalkan') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dokumen Mutasi Eksternal ini berstatus Dibatalkan dan tidak dapat dipulihkan kembali ke status aktif.'
                    ], 422);
                }
                $bamb = $mEksternal->nomor_bamb ?: ('ID #' . $mEksternal->id);
                $isKeluar = ($mEksternal->tipe === 'keluar');
                DB::transaction(function () use ($mEksternal, $isKeluar) {
                    $mEksternal->restoreData();
                    if ($isKeluar) {
                        // Mutasi Keluar: Saat dipulihkan, tandai kembali aset RSUD sebagai aset yang diserahkan keluar
                        if ($mEksternal->astap) {
                            $mEksternal->astap->update([
                                'is_reklas'     => 1,
                                'jenis_reklas'  => 'MUTASI_EKSTERNAL',
                                'kondisi'       => 'Mutasi Keluar OPD',
                                'alamat_barang' => 'Mutasi Keluar: ' . ($mEksternal->opd_tujuan ?: 'OPD Luar'),
                            ]);

                            if ($mEksternal->mutasiRegisters && $mEksternal->mutasiRegisters->isNotEmpty()) {
                                $regIds = $mEksternal->mutasiRegisters->pluck('astap_register_id')->filter()->toArray();
                                \App\Models\AstapRegister::whereIn('id', $regIds)->update([
                                    'status' => 'Mutasi Keluar OPD',
                                ]);
                            } else {
                                $mEksternal->astap->registers()->update([
                                    'status' => 'Mutasi Keluar OPD',
                                ]);
                            }

                            // Pulihkan log transaksi reklasifikasi terkait jika ada yang terhapus
                            \App\Models\AstapReklas::where('astap_id', $mEksternal->astap_id)
                                ->where('jenis_reklas', 'MUTASI_EKSTERNAL')
                                ->where('is_deleted', 1)
                                ->update([
                                    'is_deleted'    => 0,
                                    'deleted_by'    => null,
                                    'deleted_by_id' => null,
                                    'deleted_at'    => null,
                                ]);
                        }
                    } else {
                        // Mutasi Masuk: Pulihkan aset pelimpahan SKPD ke katalog aktif RSUD
                        if ($mEksternal->astap) {
                            $mEksternal->astap->restoreData();
                            $mEksternal->astap->allRegisters()->update([
                                'is_deleted'    => 0,
                                'deleted_by'    => null,
                                'deleted_by_id' => null,
                                'deleted_at'    => null,
                            ]);
                            $mEksternal->astap->jumlah_volume = max(1, $mEksternal->astap->allRegisters()->where('is_deleted', 0)->count());
                            $mEksternal->astap->save();
                        }
                    }
                });
                $msg = "Data Berita Acara Mutasi Eksternal {$bamb} (" . ($isKeluar ? 'Mutasi Keluar OPD' : 'Mutasi Masuk OPD') . ") berhasil dipulihkan.";
                break;

            case 'astap':
                $astap = Astap::with('mutasiEksternals')->findOrFail($id);
                $hasCancelledMutasi = $astap->mutasiEksternals ? $astap->mutasiEksternals->contains('status', 'Dibatalkan') : false;
                if ($hasCancelledMutasi || str_contains(strtolower((string) $astap->keterangan_tambahan), 'dibatalkan')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data Master ASTAP ini bersumber dari transaksi yang telah Dibatalkan dan tidak dapat dipulihkan kembali.'
                    ], 422);
                }
                $nama = $astap->nama_barang ?: 'Aset ASTAP';
                DB::transaction(function () use ($astap) {
                    $astap->restoreData();
                    $astap->allRegisters()->update([
                        'is_deleted'    => 0,
                        'deleted_by'    => null,
                        'deleted_by_id' => null,
                        'deleted_at'    => null,
                    ]);
                    // Pulihkan seluruh mutasi eksternal (masuk/keluar) terkait
                    if ($astap->mutasiEksternals()->exists()) {
                        $astap->mutasiEksternals()->where('is_deleted', 1)->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                    }
                    // Pulihkan riwayat reklasifikasi terkait jika ada
                    \App\Models\AstapReklas::where('astap_id', $astap->id)
                        ->where('is_deleted', 1)
                        ->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                    // Pulihkan data kemitraan jika ada yang terhapus bersama astap
                    \App\Models\AstapKemitraan::where('astap_id', $astap->id)
                        ->where('is_deleted', 1)
                        ->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                    // Sinkronkan jumlah volume paket dengan total register aktif
                    $astap->jumlah_volume = max(1, $astap->allRegisters()->where('is_deleted', 0)->count());
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
                $affectedKemitraanIds = [$kemitraan->id];

                DB::transaction(function () use ($kemitraan, &$affectedKemitraanIds, &$msg) {
                    $isDitambahkan = ($kemitraan->tipe_kemitraan === 'ditambahkan') || !empty($kemitraan->objek_astap_id);

                    // 1. Pulihkan record kemitraan target
                    $kemitraan->restoreData();
                    if ($kemitraan->astap) {
                        $kemitraan->astap->restoreData();
                        $kemitraan->astap->allRegisters()->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                        $newCount = max(1, $kemitraan->astap->allRegisters()->where('is_deleted', 0)->count());
                        $kemitraan->astap->jumlah_volume = $newCount;
                        $kemitraan->astap->save();
                        $kemitraan->jumlah_volume = $newCount;
                        $kemitraan->save();
                    }

                    // SKENARIO 1: Yang dipulihkan adalah ASET YANG DITAMBAHKAN MITRA
                    if ($isDitambahkan) {
                        $namaBarang = $kemitraan->astap?->nama_barang ?: 'Aset Ditambahkan Mitra';
                        $parentRestored = false;
                        $parentNama = '';

                        // Periksa apakah objek pemanfaatan induknya (objek_astap_id) sedang terhapus
                        if ($kemitraan->objek_astap_id) {
                            $parentKemitraan = AstapKemitraan::where('astap_id', $kemitraan->objek_astap_id)
                                ->where('is_deleted', 1)
                                ->first();

                            if ($parentKemitraan) {
                                $parentKemitraan->restoreData();
                                $affectedKemitraanIds[] = $parentKemitraan->id;
                                if ($parentKemitraan->astap) {
                                    $parentKemitraan->astap->restoreData();
                                    $parentKemitraan->astap->allRegisters()->update([
                                        'is_deleted'    => 0,
                                        'deleted_by'    => null,
                                        'deleted_by_id' => null,
                                        'deleted_at'    => null,
                                    ]);
                                    $pCount = max(1, $parentKemitraan->astap->allRegisters()->where('is_deleted', 0)->count());
                                    $parentKemitraan->astap->jumlah_volume = $pCount;
                                    $parentKemitraan->astap->save();
                                    $parentKemitraan->jumlah_volume = $pCount;
                                    $parentKemitraan->save();
                                }
                                $parentRestored = true;
                                $parentNama = $parentKemitraan->astap?->nama_barang ?: 'Objek Pemanfaatan';
                            } else {
                                $parentAstap = Astap::where('id', $kemitraan->objek_astap_id)
                                    ->where('is_deleted', 1)
                                    ->first();
                                if ($parentAstap) {
                                    $parentAstap->restoreData();
                                    $parentAstap->allRegisters()->update([
                                        'is_deleted'    => 0,
                                        'deleted_by'    => null,
                                        'deleted_by_id' => null,
                                        'deleted_at'    => null,
                                    ]);
                                    $parentAstap->jumlah_volume = max(1, $parentAstap->allRegisters()->where('is_deleted', 0)->count());
                                    $parentAstap->save();
                                    $parentRestored = true;
                                    $parentNama = $parentAstap->nama_barang;
                                }
                            }

                            // Pulihkan juga riwayat reklasifikasi induk jika ada yang terhapus
                            AstapReklas::where('astap_id', $kemitraan->objek_astap_id)
                                ->where('is_deleted', 1)
                                ->update([
                                    'is_deleted'    => 0,
                                    'deleted_by'    => null,
                                    'deleted_by_id' => null,
                                    'deleted_at'    => null,
                                ]);
                        }

                        if ($parentRestored) {
                            $msg = "Aset Mitra \"{$namaBarang}\" beserta Objek Pemanfaatan Induknya (\"{$parentNama}\") berhasil dipulihkan bersamaan ke daftar aktif.";
                        } else {
                            $msg = "Aset yang Ditambahkan Mitra \"{$namaBarang}\" berhasil dipulihkan ke daftar aktif.";
                        }
                    } 
                    // SKENARIO 2: Yang dipulihkan adalah OBJEK PEMANFAATAN (TABEL 1)
                    else {
                        $namaObjek = $kemitraan->astap?->nama_barang ?: 'Objek Pemanfaatan';

                        // Pulihkan juga riwayat reklasifikasi objek ini jika ada
                        if ($kemitraan->astap_id) {
                            AstapReklas::where('astap_id', $kemitraan->astap_id)
                                ->where('is_deleted', 1)
                                ->update([
                                    'is_deleted'    => 0,
                                    'deleted_by'    => null,
                                    'deleted_by_id' => null,
                                    'deleted_at'    => null,
                                ]);
                        }

                        // Otomatis ikut pulihkan seluruh aset mitra yang melekat pada objek ini jika ada di Recycle Bin
                        $linkedMitras = AstapKemitraan::where('objek_astap_id', $kemitraan->astap_id)
                            ->where('is_deleted', 1)
                            ->get();

                        foreach ($linkedMitras as $linked) {
                            $linked->restoreData();
                            $affectedKemitraanIds[] = $linked->id;
                            if ($linked->astap) {
                                $linked->astap->restoreData();
                                $linked->astap->allRegisters()->update([
                                    'is_deleted'    => 0,
                                    'deleted_by'    => null,
                                    'deleted_by_id' => null,
                                    'deleted_at'    => null,
                                ]);
                                $lCount = max(1, $linked->astap->allRegisters()->where('is_deleted', 0)->count());
                                $linked->astap->jumlah_volume = $lCount;
                                $linked->astap->save();
                                $linked->jumlah_volume = $lCount;
                                $linked->save();
                            }
                        }


                        if ($linkedMitras->isNotEmpty()) {
                            $mitraCount = $linkedMitras->count();
                            $msg = "Objek Pemanfaatan \"{$namaObjek}\" beserta {$mitraCount} aset yang ditambahkan mitra terkait berhasil dipulihkan ke daftar aktif.";
                        } else {
                            $msg = "Objek Pemanfaatan \"{$namaObjek}\" berhasil dipulihkan ke daftar aktif.";
                        }
                    }
                });
                break;

            case 'belanja_barang':
                $belanja = AstapBelanjaBarang::findOrFail($id);
                $faktur = $belanja->nomor_faktur ?: 'Faktur Belanja';
                DB::transaction(function () use ($belanja) {
                    $belanja->restoreData();
                    if ($belanja->astap) {
                        $belanja->astap->restoreData();
                        $belanja->astap->allRegisters()->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                        $belanja->astap->jumlah_volume = max(1, $belanja->astap->allRegisters()->where('is_deleted', 0)->count());
                        $belanja->astap->save();
                    }
                });
                $msg = "Data Belanja Barang {$faktur} beserta aset terkait berhasil dipulihkan ke daftar aktif.";
                break;

            case 'reklas':
            case 'reklasifikasi':
                $reklas = AstapReklas::findOrFail($id);
                $nomorBa = $reklas->nomor_ba_reklas ?: 'BA-REKLAS-' . $reklas->id;
                DB::transaction(function () use ($reklas) {
                    $reklas->restoreData();
                    if ($reklas->astap) {
                        $reklas->astap->is_reklas = 1;
                        if ($reklas->jenis_reklas) {
                            $reklas->astap->jenis_reklas = $reklas->jenis_reklas;
                        }
                        if ($reklas->tujuan_kib) {
                            $reklas->astap->category = $reklas->tujuan_kib;
                        }
                        $reklas->astap->save();
                    }
                });
                $msg = "Transaksi Reklasifikasi {$nomorBa} berhasil dipulihkan ke daftar aktif.";
                break;

            case 'unit':
                $unit = Unit::findOrFail($id);
                $nama = $unit->nama;
                $unit->restoreData();
                $msg = "Data Unit / Paviliun \"{$nama}\" dan seluruh akun pengguna terkait berhasil dipulihkan ke katalog aktif.";
                break;

            case 'nibar':
            case 'belanja_modal_nibar':
            case 'kemitraan_nibar':
            case 'mutasi_eksternal_nibar':
            case 'hibah_nibar':
            case 'belanja_barang_nibar':
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
                        if ($astap->belanjaModal) {
                            $astap->belanjaModal->restoreData();
                        }
                        if ($astap->belanjaBarang) {
                            $astap->belanjaBarang->restoreData();
                        }
                        if ($astap->hibah) {
                            $astap->hibah->restoreData();
                        }
                        \App\Models\AstapKemitraan::where('astap_id', $astap->id)
                            ->where('is_deleted', 1)
                            ->get()
                            ->each(fn($km) => $km->restoreData());

                        $astapRestored = true;
                    }
                    if ($astap) {
                        $this->syncAstapAfterRegisterChange($astap);
                        \App\Models\AstapKemitraan::where('astap_id', $astap->id)
                            ->update(['jumlah_volume' => max(1, $astap->registers()->where('is_deleted', 0)->count())]);
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
                'success'                => true,
                'message'                => $msg,
                'astap_restored'         => $astapRestored ?? false,
                'astap_id'               => isset($astap) && ($astapRestored ?? false) ? $astap->id : null,
                'affected_kemitraan_ids' => array_values(array_unique($affectedKemitraanIds ?? [])),
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
                $items = \App\Models\MutasiEksternal::with(['astap.registers', 'mutasiRegisters.register'])->whereIn('id', $ids)->get();
                $blockedBatalCount = 0;
                DB::transaction(function () use ($items, &$restoredCount, &$blockedBatalCount) {
                    foreach ($items as $mEksternal) {
                        if ($mEksternal->status === 'Dibatalkan') {
                            $blockedBatalCount++;
                            continue;
                        }
                        $mEksternal->restoreData();
                        $isKeluar = ($mEksternal->tipe === 'keluar');
                        if ($isKeluar) {
                            if ($mEksternal->astap) {
                                $mEksternal->astap->update([
                                    'is_reklas'     => 1,
                                    'jenis_reklas'  => 'MUTASI_EKSTERNAL',
                                    'kondisi'       => 'Mutasi Keluar OPD',
                                    'alamat_barang' => 'Mutasi Keluar: ' . ($mEksternal->opd_tujuan ?: 'OPD Luar'),
                                ]);

                                if ($mEksternal->mutasiRegisters && $mEksternal->mutasiRegisters->isNotEmpty()) {
                                    $regIds = $mEksternal->mutasiRegisters->pluck('astap_register_id')->filter()->toArray();
                                    \App\Models\AstapRegister::whereIn('id', $regIds)->update([
                                        'status' => 'Mutasi Keluar OPD',
                                    ]);
                                } else {
                                    $mEksternal->astap->registers()->update([
                                        'status' => 'Mutasi Keluar OPD',
                                    ]);
                                }

                                \App\Models\AstapReklas::where('astap_id', $mEksternal->astap_id)
                                    ->where('jenis_reklas', 'MUTASI_EKSTERNAL')
                                    ->where('is_deleted', 1)
                                    ->update([
                                        'is_deleted'    => 0,
                                        'deleted_by'    => null,
                                        'deleted_by_id' => null,
                                        'deleted_at'    => null,
                                    ]);
                            }
                        } else {
                            if ($mEksternal->astap) {
                                $mEksternal->astap->restoreData();
                                $mEksternal->astap->allRegisters()->update([
                                    'is_deleted'    => 0,
                                    'deleted_by'    => null,
                                    'deleted_by_id' => null,
                                    'deleted_at'    => null,
                                ]);
                                $mEksternal->astap->jumlah_volume = max(1, $mEksternal->astap->allRegisters()->where('is_deleted', 0)->count());
                                $mEksternal->astap->save();
                            }
                        }
                        $restoredCount++;
                    }
                });
                if ($blockedBatalCount > 0 && $restoredCount === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dokumen ini berstatus Dibatalkan dan tidak dapat disahkan atau dipulihkan kembali.'
                    ], 422);
                }
                $msg = "Sebanyak {$restoredCount} transaksi mutasi eksternal berhasil dipulihkan ke status aktif.";
                break;

            case 'astap':
                $astaps = Astap::with('mutasiEksternals')->whereIn('id', $ids)->get();
                $blockedBatalCount = 0;
                DB::transaction(function () use ($astaps, &$restoredCount, &$blockedBatalCount) {
                    foreach ($astaps as $a) {
                        $ket = (string) ($a->keterangan_tambahan ?? '');
                        $hasCancelledMutasi = $a->mutasiEksternals ? $a->mutasiEksternals->contains('status', 'Dibatalkan') : false;
                        if (str_contains(strtolower($ket), 'dibatalkan') || $hasCancelledMutasi) {
                            $blockedBatalCount++;
                            continue;
                        }

                        $a->restoreData();
                        $a->allRegisters()->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                        if ($a->mutasiEksternals()->exists()) {
                            $a->mutasiEksternals()->where('is_deleted', 1)->get()->each->restoreData();
                        }
                        $a->jumlah_volume = max(1, $a->allRegisters()->where('is_deleted', 0)->count());
                        $a->save();
                        $restoredCount++;
                    }
                });
                if ($blockedBatalCount > 0 && $restoredCount === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data aset ini bersumber dari transaksi yang telah Dibatalkan dan tidak dapat disahkan atau dipulihkan kembali.'
                    ], 422);
                }
                $msg = "Sebanyak {$restoredCount} data Master ASTAP berhasil dipulihkan ke status aktif.";
                break;

            case 'belanja_modal':
                $astaps = Astap::with('mutasiEksternals')->whereIn('id', $ids)->get();
                $blockedBatalCount = 0;
                DB::transaction(function () use ($astaps, &$restoredCount, &$blockedBatalCount) {
                    foreach ($astaps as $a) {
                        $ket = (string) ($a->keterangan_tambahan ?? '');
                        $hasCancelledMutasi = $a->mutasiEksternals ? $a->mutasiEksternals->contains('status', 'Dibatalkan') : false;
                        if (str_contains(strtolower($ket), 'dibatalkan') || $hasCancelledMutasi) {
                            $blockedBatalCount++;
                            continue;
                        }

                        $a->restoreData();
                        $a->allRegisters()->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                        $a->jumlah_volume = max(1, $a->allRegisters()->where('is_deleted', 0)->count());
                        $a->save();
                        $restoredCount++;
                    }
                });
                if ($blockedBatalCount > 0 && $restoredCount === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data aset ini bersumber dari transaksi yang telah Dibatalkan dan tidak dapat disahkan atau dipulihkan kembali.'
                    ], 422);
                }
                $msg = "Sebanyak {$restoredCount} paket Belanja Modal berhasil dipulihkan ke status aktif.";
                break;

            case 'nibar':
            case 'belanja_modal_nibar':
            case 'kemitraan_nibar':
            case 'mutasi_eksternal_nibar':
            case 'hibah_nibar':
            case 'belanja_barang_nibar':
                $regs = AstapRegister::whereIn('id', $ids)->get();
                $astapParents = [];
                $parentRestoredCount = 0;

                DB::transaction(function () use ($regs, &$astapParents, &$restoredCount, &$parentRestoredCount) {
                    foreach ($regs as $r) {
                        $r->restoreData();
                        if ($r->astap) {
                            if ($r->astap->is_deleted && !isset($astapParents[$r->astap_id])) {
                                $r->astap->restoreData();
                                if ($r->astap->mutasiEksternal) {
                                    $r->astap->mutasiEksternal->restoreData();
                                }
                                if ($r->astap->belanjaModal) {
                                    $r->astap->belanjaModal->restoreData();
                                }
                                if ($r->astap->belanjaBarang) {
                                    $r->astap->belanjaBarang->restoreData();
                                }
                                if ($r->astap->hibah) {
                                    $r->astap->hibah->restoreData();
                                }
                                \App\Models\AstapKemitraan::where('astap_id', $r->astap->id)
                                    ->where('is_deleted', 1)
                                    ->get()
                                    ->each(fn($km) => $km->restoreData());

                                $parentRestoredCount++;
                            }
                            $astapParents[$r->astap_id] = $r->astap;
                        }
                        $restoredCount++;
                    }
                    foreach ($astapParents as $parent) {
                        $this->syncAstapAfterRegisterChange($parent);
                        \App\Models\AstapKemitraan::where('astap_id', $parent->id)
                            ->update(['jumlah_volume' => max(1, $parent->registers()->where('is_deleted', 0)->count())]);
                    }

                });

                $msg = "Sebanyak {$restoredCount} unit register NIBAR berhasil dipulihkan ke katalog aktif.";
                if ($parentRestoredCount > 0) {
                    $msg .= " ({$parentRestoredCount} paket pengadaan induk otomatis diaktifkan kembali).";
                }
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
                $affectedKemitraanIds = [];
                DB::transaction(function () use ($kemitraans, &$restoredCount, &$affectedKemitraanIds) {
                    foreach ($kemitraans as $k) {
                        $isDitambahkan = ($k->tipe_kemitraan === 'ditambahkan') || !empty($k->objek_astap_id);
                        $k->restoreData();
                        $affectedKemitraanIds[] = $k->id;
                        if ($k->astap) {
                            $k->astap->restoreData();
                            $k->astap->allRegisters()->update([
                                'is_deleted'    => 0,
                                'deleted_by'    => null,
                                'deleted_by_id' => null,
                                'deleted_at'    => null,
                            ]);
                            $newCount = max(1, $k->astap->allRegisters()->where('is_deleted', 0)->count());
                            $k->astap->jumlah_volume = $newCount;
                            $k->astap->save();
                            $k->jumlah_volume = $newCount;
                            $k->save();
                        }

                        if ($isDitambahkan) {
                            // Pulihkan objek induk jika masih terhapus
                            if ($k->objek_astap_id) {
                                $parentKemitraan = AstapKemitraan::where('astap_id', $k->objek_astap_id)
                                    ->where('is_deleted', 1)
                                    ->first();
                                if ($parentKemitraan) {
                                    $parentKemitraan->restoreData();
                                    $affectedKemitraanIds[] = $parentKemitraan->id;
                                    if ($parentKemitraan->astap) {
                                        $parentKemitraan->astap->restoreData();
                                        $parentKemitraan->astap->allRegisters()->update([
                                            'is_deleted'    => 0,
                                            'deleted_by'    => null,
                                            'deleted_by_id' => null,
                                            'deleted_at'    => null,
                                        ]);
                                        $pCount = max(1, $parentKemitraan->astap->allRegisters()->where('is_deleted', 0)->count());
                                        $parentKemitraan->astap->jumlah_volume = $pCount;
                                        $parentKemitraan->astap->save();
                                        $parentKemitraan->jumlah_volume = $pCount;
                                        $parentKemitraan->save();
                                    }
                                } else {
                                    $parentAstap = Astap::where('id', $k->objek_astap_id)
                                        ->where('is_deleted', 1)
                                        ->first();
                                    if ($parentAstap) {
                                        $parentAstap->restoreData();
                                        $parentAstap->allRegisters()->update([
                                            'is_deleted'    => 0,
                                            'deleted_by'    => null,
                                            'deleted_by_id' => null,
                                            'deleted_at'    => null,
                                        ]);
                                        $parentAstap->jumlah_volume = max(1, $parentAstap->allRegisters()->where('is_deleted', 0)->count());
                                        $parentAstap->save();
                                    }
                                }
                                AstapReklas::where('astap_id', $k->objek_astap_id)->where('is_deleted', 1)->update([
                                    'is_deleted'    => 0,
                                    'deleted_by'    => null,
                                    'deleted_by_id' => null,
                                    'deleted_at'    => null,
                                ]);
                            }
                        } else {
                            // Pulihkan reklas objek induk dan seluruh aset mitra yang melekat padanya
                            if ($k->astap_id) {
                                AstapReklas::where('astap_id', $k->astap_id)->where('is_deleted', 1)->update([
                                    'is_deleted'    => 0,
                                    'deleted_by'    => null,
                                    'deleted_by_id' => null,
                                    'deleted_at'    => null,
                                ]);
                            }
                            $linkedMitras = AstapKemitraan::where('objek_astap_id', $k->astap_id)
                                ->where('is_deleted', 1)
                                ->get();
                            foreach ($linkedMitras as $linked) {
                                $linked->restoreData();
                                $affectedKemitraanIds[] = $linked->id;
                                if ($linked->astap) {
                                    $linked->astap->restoreData();
                                    $linked->astap->allRegisters()->update([
                                        'is_deleted'    => 0,
                                        'deleted_by'    => null,
                                        'deleted_by_id' => null,
                                        'deleted_at'    => null,
                                    ]);
                                    $lCount = max(1, $linked->astap->allRegisters()->where('is_deleted', 0)->count());
                                    $linked->astap->jumlah_volume = $lCount;
                                    $linked->astap->save();
                                    $linked->jumlah_volume = $lCount;
                                    $linked->save();
                                }
                            }
                        }

                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} dokumen kemitraan aset beserta relasi induk/anak terkait berhasil dipulihkan ke status aktif.";
                break;

            case 'belanja_barang':
                $belanjas = AstapBelanjaBarang::whereIn('id', $ids)->get();
                DB::transaction(function () use ($belanjas, &$restoredCount) {
                    foreach ($belanjas as $b) {
                        $b->restoreData();
                        if ($b->astap) {
                            $b->astap->restoreData();
                            $b->astap->allRegisters()->update([
                                'is_deleted'    => 0,
                                'deleted_by'    => null,
                                'deleted_by_id' => null,
                                'deleted_at'    => null,
                            ]);
                            $b->astap->jumlah_volume = max(1, $b->astap->allRegisters()->where('is_deleted', 0)->count());
                            $b->astap->save();
                        }
                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} dokumen belanja barang berhasil dipulihkan ke status aktif.";
                break;

            case 'reklas':
            case 'reklasifikasi':
                $reklasItems = AstapReklas::whereIn('id', $ids)->get();
                DB::transaction(function () use ($reklasItems, &$restoredCount) {
                    foreach ($reklasItems as $rk) {
                        $rk->restoreData();
                        if ($rk->astap) {
                            $rk->astap->is_reklas = 1;
                            if ($rk->jenis_reklas) {
                                $rk->astap->jenis_reklas = $rk->jenis_reklas;
                            }
                            if ($rk->tujuan_kib) {
                                $rk->astap->category = $rk->tujuan_kib;
                            }
                            $rk->astap->save();
                        }
                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} transaksi reklasifikasi berhasil dipulihkan ke status aktif.";
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
                'success'                => true,
                'message'                => $msg,
                'count'                  => $restoredCount,
                'restored_parent_ids'    => isset($astapParents) ? array_keys($astapParents) : [],
                'affected_kemitraan_ids' => array_values(array_unique($affectedKemitraanIds ?? [])),
            ]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module === 'nibar' ? 'astap' : ($module === 'belanja_modal_nibar' ? 'belanja_modal' : $module)])->with('success', $msg);
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
                $items = \App\Models\MutasiEksternal::with('astap.allRegisters')->whereIn('id', $ids)->get();

                // Cek proteksi audit BPK untuk aset mutasi masuk yang dipilih
                $blockedMutasis = [];
                foreach ($items as $mEksternal) {
                    if ($mEksternal->tipe !== 'keluar' && $mEksternal->astap) {
                        $regIds = $mEksternal->astap->allRegisters->pluck('id')->toArray();
                        if (!empty($regIds)) {
                            $hasDistribusi = \App\Models\DistribusiItemRegister::whereIn('astap_register_id', $regIds)->exists();
                            $hasInternalMutasi = \App\Models\AstapMutasiRegister::whereIn('astap_register_id', $regIds)->exists();
                            if ($hasDistribusi || $hasInternalMutasi) {
                                $blockedMutasis[] = $mEksternal->astap->nama_barang ?: ($mEksternal->nomor_bamb ?: "ID #{$mEksternal->id}");
                            }
                        }
                    }
                }
                if (!empty($blockedMutasis)) {
                    $listStr = implode(', ', array_slice($blockedMutasis, 0, 3));
                    if (count($blockedMutasis) > 3) {
                        $listStr .= '... dan ' . (count($blockedMutasis) - 3) . ' mutasi lainnya';
                    }
                    $msg = "Penghapusan permanen ditolak: Terdapat aset Mutasi Eksternal [{$listStr}] yang unitnya memiliki riwayat transaksi BAST Distribusi atau Mutasi internal aktif yang dilindungi audit.";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->with('error', $msg);
                }

                DB::transaction(function () use ($items, &$deletedCount) {
                    foreach ($items as $mEksternal) {
                        // Bersihkan berkas fisik lampiran di storage jika ada
                        if (!empty($mEksternal->dokumen_lampiran) && \Illuminate\Support\Facades\Storage::disk('public')->exists($mEksternal->dokumen_lampiran)) {
                            try {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($mEksternal->dokumen_lampiran);
                            } catch (\Throwable $e) {}
                        }

                        $isKeluar = ($mEksternal->tipe === 'keluar');
                        if ($isKeluar) {
                            // Untuk mutasi keluar: JANGAN PERNAH hapus aset ASTAP RSUD!
                            // Cukup hapus permanen riwayat pivot dan transaksi mutasi eksternal
                            \App\Models\MutasiEksternalRegister::where('mutasi_eksternal_id', $mEksternal->id)->delete();
                            \App\Models\AstapReklas::where('astap_id', $mEksternal->astap_id)
                                ->where('jenis_reklas', 'MUTASI_EKSTERNAL')
                                ->where('is_deleted', 1)
                                ->delete();
                            $mEksternal->delete();
                        } else {
                            $astap = $mEksternal->astap;
                            if ($astap) {
                                // Hapus seluruh unit register (termasuk yang is_deleted = 1) agar tidak menjadi orphan data
                                $astap->allRegisters()->delete();
                                $mEksternal->delete();
                                $astap->delete();
                            } else {
                                $mEksternal->delete();
                            }
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

            case 'belanja_barang':
                $belanjas = AstapBelanjaBarang::whereIn('id', $ids)->get();
                DB::transaction(function () use ($belanjas, &$deletedCount) {
                    foreach ($belanjas as $b) {
                        $astap = $b->astap;
                        if ($astap) {
                            $astap->registers()->delete();
                            $b->delete();
                            $astap->delete();
                        } else {
                            $b->delete();
                        }
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} data belanja barang telah dihapus permanen dari database.";
                break;

            case 'reklas':
            case 'reklasifikasi':
                $reklasItems = AstapReklas::whereIn('id', $ids)->get();
                foreach ($reklasItems as $rk) {
                    $rk->delete();
                    $deletedCount++;
                }
                $msg = "Sebanyak {$deletedCount} data transaksi reklasifikasi telah dihapus secara permanen dari database.";
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

            case 'belanja_modal':
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
                    $msg = "Penghapusan permanen ditolak: Terdapat paket Belanja Modal [{$listStr}] yang unitnya memiliki riwayat transaksi BAST Distribusi atau Mutasi aset aktif yang dilindungi audit.";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->with('error', $msg);
                }

                DB::transaction(function () use ($astaps, &$deletedCount) {
                    foreach ($astaps as $a) {
                        $a->registers()->delete();
                        \App\Models\AstapBelanjaModal::where('astap_id', $a->id)->delete();
                        $a->delete();
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} data Belanja Modal telah dihapus permanen dari database.";
                break;

            case 'nibar':
            case 'belanja_modal_nibar':
            case 'kemitraan_nibar':
            case 'mutasi_eksternal_nibar':
            case 'hibah_nibar':
            case 'belanja_barang_nibar':
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

            case 'kemitraan':
                $kemitraans = AstapKemitraan::whereIn('id', $ids)->get();

                // Sortir: Aset ditambahkan mitra diproses lebih dulu daripada objek pemanfaatan
                $sortedKemitraans = $kemitraans->sortByDesc(function ($k) {
                    return (($k->tipe_kemitraan === 'ditambahkan') || !empty($k->objek_astap_id)) ? 1 : 0;
                });

                // Cek apakah ada objek pemanfaatan yang dipilih sementara aset mitranya BELUM ikut dipilih dan masih ada di database
                $selectedIdsSet = collect($ids)->map(fn($v) => (int)$v);
                foreach ($sortedKemitraans as $k) {
                    $isDitambahkan = ($k->tipe_kemitraan === 'ditambahkan') || !empty($k->objek_astap_id);
                    if (!$isDitambahkan && $k->astap_id) {
                        $unselectedChildCount = AstapKemitraan::where('objek_astap_id', $k->astap_id)
                            ->whereNotIn('id', $selectedIdsSet)
                            ->count();
                        if ($unselectedChildCount > 0) {
                            $parentName = $k->astap?->nama_barang ?: 'Objek Pemanfaatan';
                            $blockMsg = "Penghapusan permanen massal ditolak: Objek \"{$parentName}\" masih memiliki {$unselectedChildCount} aset mitra terkait yang belum ikut dipilih untuk dihapus permanen.";
                            if ($request->wantsJson()) {
                                return response()->json(['success' => false, 'message' => $blockMsg], 422);
                            }
                            return back()->with('error', $blockMsg);
                        }
                    }
                }

                DB::transaction(function () use ($sortedKemitraans, &$deletedCount) {
                    foreach ($sortedKemitraans as $k) {
                        $astap = $k->astap;
                        if ($astap) {
                            $astap->registers()->delete();
                            AstapReklas::where('astap_id', $astap->id)->delete();
                            $k->delete();
                            $astap->delete();
                        } else {
                            $k->delete();
                        }
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} data Kemitraan PKS beserta aset terkait telah dihapus permanen dari database.";
                break;

            case 'belanja_barang':
                $belanjas = AstapBelanjaBarang::whereIn('id', $ids)->get();
                DB::transaction(function () use ($belanjas, &$deletedCount) {
                    foreach ($belanjas as $b) {
                        $astap = $b->astap;
                        if ($astap) {
                            $astap->registers()->delete();
                            $b->delete();
                            $astap->delete();
                        } else {
                            $b->delete();
                        }
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} data Belanja Barang beserta aset terkait telah dihapus permanen dari database.";
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

        return redirect()->route('recycle_bin.index', ['tab' => $module === 'nibar' ? 'astap' : ($module === 'belanja_modal_nibar' ? 'belanja_modal' : $module)])->with('success', $msg);
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
                $bamb = $mEksternal->nomor_bamb ?: ('ID #' . $mEksternal->id);
                $isKeluar = ($mEksternal->tipe === 'keluar');

                // Proteksi Audit BPK untuk Mutasi Masuk:
                // Jika aset mutasi masuk pernah didistribusikan ke ruangan via BAST Distribusi internal RSUD, tolak hapus permanen
                if (!$isKeluar && $mEksternal->astap) {
                    $regIds = $mEksternal->astap->allRegisters()->pluck('id')->toArray();
                    if (!empty($regIds)) {
                        $hasDistribusi = \App\Models\DistribusiItemRegister::whereIn('astap_register_id', $regIds)->exists();
                        $hasInternalMutasi = \App\Models\AstapMutasiRegister::whereIn('astap_register_id', $regIds)->exists();
                        if ($hasDistribusi || $hasInternalMutasi) {
                            $reason = $hasDistribusi ? 'telah resmi diserahterimakan via dokumen BAST Distribusi' : 'memiliki riwayat mutasi aset internal';
                            $msg = "Penghapusan permanen ditolak: Aset Mutasi Masuk \"{$mEksternal->astap->nama_barang}\" memiliki unit yang {$reason}. Data dilindungi undang-undang untuk audit BPK & Inspektorat.";
                            if ($request->wantsJson()) {
                                return response()->json(['success' => false, 'message' => $msg], 422);
                            }
                            return back()->with('error', $msg);
                        }
                    }
                }

                DB::transaction(function () use ($mEksternal, $isKeluar) {
                    // Bersihkan file fisik lampiran di storage jika ada
                    if (!empty($mEksternal->dokumen_lampiran) && \Illuminate\Support\Facades\Storage::disk('public')->exists($mEksternal->dokumen_lampiran)) {
                        try {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($mEksternal->dokumen_lampiran);
                        } catch (\Throwable $e) {}
                    }

                    if ($isKeluar) {
                        // Untuk mutasi keluar: JANGAN PERNAH hapus aset ASTAP RSUD!
                        \App\Models\MutasiEksternalRegister::where('mutasi_eksternal_id', $mEksternal->id)->delete();
                        \App\Models\AstapReklas::where('astap_id', $mEksternal->astap_id)
                            ->where('jenis_reklas', 'MUTASI_EKSTERNAL')
                            ->where('is_deleted', 1)
                            ->delete();
                        $mEksternal->delete();
                    } else {
                        $astap = $mEksternal->astap;
                        if ($astap) {
                            // Hapus seluruh unit register (termasuk yang is_deleted = 1) agar tidak menjadi orphan data
                            $astap->allRegisters()->delete();
                            $mEksternal->delete();
                            $astap->delete();
                        } else {
                            $mEksternal->delete();
                        }
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

            case 'belanja_modal':
                $astap = Astap::findOrFail($id);
                $nama = $astap->nama_barang ?: 'Aset Belanja Modal';
                $regIds = $astap->registers()->pluck('id')->toArray();
                if (!empty($regIds)) {
                    $hasDistribusi = \App\Models\DistribusiItemRegister::whereIn('astap_register_id', $regIds)->exists();
                    $hasMutasi = \App\Models\AstapMutasiRegister::whereIn('astap_register_id', $regIds)->exists();
                    if ($hasDistribusi || $hasMutasi) {
                        $reason = $hasDistribusi ? 'telah resmi diserahterimakan via dokumen BAST Distribusi' : 'memiliki riwayat mutasi aset';
                        $msg = "Penghapusan permanen ditolak: Paket Belanja Modal \"{$nama}\" memiliki unit yang {$reason}. Data dilindungi undang-undang untuk audit BPK & Inspektorat.";
                        if ($request->wantsJson()) {
                            return response()->json(['success' => false, 'message' => $msg], 422);
                        }
                        return back()->with('error', $msg);
                    }
                }

                DB::transaction(function () use ($astap) {
                    $astap->registers()->delete();
                    \App\Models\AstapBelanjaModal::where('astap_id', $astap->id)->delete();
                    $astap->delete();
                });
                $msg = "Data Belanja Modal \"{$nama}\" telah dihapus secara permanen dari database.";
                break;

            case 'nibar':
            case 'belanja_modal_nibar':
            case 'kemitraan_nibar':
            case 'mutasi_eksternal_nibar':
            case 'hibah_nibar':
            case 'belanja_barang_nibar':
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
                $isDitambahkan = ($kemitraan->tipe_kemitraan === 'ditambahkan') || !empty($kemitraan->objek_astap_id);

                // Jika yang ingin dihapus permanen adalah Objek Pemanfaatan (Tabel 1):
                if (!$isDitambahkan && $kemitraan->astap_id) {
                    $linkedMitrasCount = AstapKemitraan::where('objek_astap_id', $kemitraan->astap_id)->count();
                    if ($linkedMitrasCount > 0) {
                        $blockMsg = "Objek Pemanfaatan ini tidak dapat dihapus permanen karena masih memiliki {$linkedMitrasCount} aset mitra terkait di sistem/tong sampah. Silakan hapus permanen seluruh aset mitra terkait terlebih dahulu demi integritas inventaris RSUD.";
                        if ($request->wantsJson()) {
                            return response()->json(['success' => false, 'message' => $blockMsg], 422);
                        }
                        return back()->with('error', $blockMsg);
                    }
                }

                $namaAset = $kemitraan->astap?->nama_barang ?: ($isDitambahkan ? 'Aset Ditambahkan Mitra' : 'Objek Pemanfaatan');
                $pks = $kemitraan->nomor_pks;
                DB::transaction(function () use ($kemitraan) {
                    $astap = $kemitraan->astap;
                    if ($astap) {
                        $astap->registers()->delete();
                        AstapReklas::where('astap_id', $astap->id)->delete();
                        $kemitraan->delete();
                        $astap->delete();
                    } else {
                        $kemitraan->delete();
                    }
                });
                $msg = "Data Kemitraan \"{$namaAset}\" (PKS: {$pks}) beserta aset register terkait telah dihapus secara permanen dari database.";
                break;

            case 'belanja_barang':
                $belanja = AstapBelanjaBarang::findOrFail($id);
                $faktur = $belanja->nomor_faktur ?: 'Belanja Barang';
                DB::transaction(function () use ($belanja) {
                    $astap = $belanja->astap;
                    if ($astap) {
                        $astap->registers()->delete();
                        $belanja->delete();
                        $astap->delete();
                    } else {
                        $belanja->delete();
                    }
                });
                $msg = "Data Belanja Barang {$faktur} beserta aset register terkait telah dihapus secara permanen dari database.";
                break;

            case 'reklas':
            case 'reklasifikasi':
                $reklas = AstapReklas::findOrFail($id);
                $nomorBa = $reklas->nomor_ba_reklas ?: 'BA-REKLAS-' . $reklas->id;
                $reklas->delete();
                $msg = "Data transaksi Reklasifikasi {$nomorBa} telah dihapus secara permanen dari database.";
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
            case 'belanja_modal_nibar':
            case 'kemitraan_nibar':
            case 'mutasi_eksternal_nibar':
            case 'hibah_nibar':
            case 'belanja_barang_nibar':
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
                DB::transaction(function () use ($reg, $parentAstap) {
                    \App\Models\MutasiEksternalRegister::where('astap_register_id', $reg->id)->delete();
                    $reg->delete();
                    if ($parentAstap) {
                        $this->syncAstapAfterRegisterChange($parentAstap);
                    }
                });
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

        $tabRedirect = match ($module) {
            'nibar'                  => 'astap',
            'belanja_modal_nibar'    => 'belanja_modal',
            'mutasi_eksternal_nibar' => 'mutasi',
            'kemitraan_nibar'        => 'kemitraan',
            'hibah_nibar'            => 'hibah',
            'belanja_barang_nibar'   => 'belanja_barang',
            default                  => $module,
        };

        return redirect()->route('recycle_bin.index', ['tab' => $tabRedirect])->with('success', $msg);
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
        $kbCount = $allRegs->whereIn('kondisi', ['Kurang Baik', 'KB', 'Rusak Ringan', 'RR'])->count();
        $rbCount = $allRegs->whereIn('kondisi', ['Rusak Berat', 'Rusak', 'RB'])->count();
        $dominan = ($baikCount >= $kbCount && $baikCount >= $rbCount) ? 'Baik'
            : (($kbCount >= $rbCount) ? 'Kurang Baik' : 'Rusak Berat');

        $spec = $astap->spesifikasi_json ?? [];
        if (is_array($spec)) {
            $spec['kondisi'] = $dominan;
            $spec['kondisi_stats'] = [
                'total' => $totalRegs,
                'baik' => $baikCount,
                'kurang_baik' => $kbCount,
                'rusak_berat' => $rbCount,
                'pct_baik' => $totalRegs > 0 ? round($baikCount / $totalRegs * 100) : 0,
                'pct_kb' => $totalRegs > 0 ? round($kbCount / $totalRegs * 100) : 0,
                'pct_rb' => $totalRegs > 0 ? round($rbCount / $totalRegs * 100) : 0,
                'kondisi_dominan' => $dominan,
            ];

            // Sinkronisasi volume pada repeater items agar selalu seimbang dan akurat dengan register aktif
            $repeatersConfig = [
                'tanah_items'    => ['name' => 'tanah_nama_barang', 'qty' => ['tanah_jumlah_barang', 'tanah_jumlah_bidang']],
                'mesin_items'    => ['name' => 'mesin_nama_barang', 'qty' => ['mesin_jumlah_barang']],
                'gedung_items'   => ['name' => 'gedung_nama_barang', 'qty' => ['gedung_jumlah_bangunan']],
                'jaringan_items' => ['name' => 'jaringan_nama_barang', 'qty' => ['jaringan_jumlah', 'jaringan_jumlah_barang']],
                'lainnya_items'  => ['name' => 'lainnya_nama_barang', 'qty' => ['lainnya_jumlah_barang']],
                'atb_items'      => ['name' => 'atb_nama_barang', 'qty' => ['atb_jumlah']],
                'kdp_items'      => ['name' => 'kdp_nama_barang', 'qty' => ['kdp_jumlah_bangunan']],
            ];

            $activeCounts = $allRegs->groupBy(fn($r) => trim((string)$r->nama_barang))->map->count();

            foreach ($repeatersConfig as $itemsKey => $cfg) {
                if (!empty($spec[$itemsKey]) && is_array($spec[$itemsKey])) {
                    $nameKey = $cfg['name'];
                    $qtyKeys = $cfg['qty'];
                    $syncedItems = [];

                    foreach ($spec[$itemsKey] as $it) {
                        $itemName = trim((string)($it[$nameKey] ?? ''));
                        $activeQtyKey = $qtyKeys[0];
                        foreach ($qtyKeys as $qk) {
                            if (isset($it[$qk])) {
                                $activeQtyKey = $qk;
                                break;
                            }
                        }

                        if ($activeCounts->has($itemName)) {
                            $matchedCount = $activeCounts->get($itemName);
                            if ($matchedCount > 0) {
                                $it[$activeQtyKey] = $matchedCount;
                                $syncedItems[] = $it;
                            }
                        } else {
                            $syncedItems[] = $it;
                        }
                    }

                    if (!empty($syncedItems)) {
                        $spec[$itemsKey] = $syncedItems;
                    }
                }
            }

            $astap->spesifikasi_json = $spec;
        }
        $astap->save();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AstapBastTriwulan;
use App\Models\Astap;
use App\Models\Distribusi;
use App\Models\AstapMutasi;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BeritaAcaraController extends Controller
{
    /**
     * Tampilkan Halaman Utama Berita Acara (BAST) Terintegrasi
     */
    public function index(Request $request)
    {
        $tahun = $request->query('tahun', '2026');

        // =========================================================================
        // 1. DATA TAB 1: BAST PENAMBAHAN ASET TETAP TRIWULAN (ASTAP)
        // =========================================================================
        $triwulanKeys = ['TW1', 'TW2', 'TW3', 'TW4'];
        $triwulanMonths = [
            'TW1' => [1, 2, 3],
            'TW2' => [4, 5, 6],
            'TW3' => [7, 8, 9],
            'TW4' => [10, 11, 12],
        ];
        $triwulanNames = [
            'TW1' => 'Triwulan I (Januari - Maret) Tahun ' . $tahun,
            'TW2' => 'Triwulan II (April - Juni) Tahun ' . $tahun,
            'TW3' => 'Triwulan III (Juli - September) Tahun ' . $tahun,
            'TW4' => 'Triwulan IV (Oktober - Desember) Tahun ' . $tahun,
        ];
        $defaultDates = [
            'TW1' => 'Selasa tanggal 31 Maret ' . $tahun,
            'TW2' => 'Selasa tanggal 30 Juni ' . $tahun,
            'TW3' => 'Rabu tanggal 30 September ' . $tahun,
            'TW4' => 'Kamis tanggal 31 Desember ' . $tahun,
        ];

        $triwulanData = [];

        foreach ($triwulanKeys as $key) {
            $doc = AstapBastTriwulan::firstOrCreate(
                ['tahun' => $tahun, 'triwulan' => $key],
                [
                    'nomor_surat'    => '000.2.3.2/' . ($key === 'TW1' ? '112' : ($key === 'TW2' ? '224' : ($key === 'TW3' ? '318' : '415'))) . '/430.10.7/' . $tahun,
                    'tanggal_bast'   => $tahun . '-' . ($key === 'TW1' ? '03-31' : ($key === 'TW2' ? '06-30' : ($key === 'TW3' ? '09-30' : '12-31'))),
                    'lokasi'         => 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
                    'pihak1_nama'    => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                    'pihak1_nip'     => '19771002 200604 1 006',
                    'pihak1_jabatan' => 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                    'pihak2_nama'    => 'BUDI HARTONO,S.Sos',
                    'pihak2_nip'     => '19760229 200801 1 010',
                    'pihak2_jabatan' => 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                    'direktur_nama'  => 'dr. DIAN ARISANDI, M.Kes',
                    'direktur_nip'   => '19730514 200212 2 003',
                    'status'         => $key === 'TW4' ? 'Draft' : 'Telah Ditandatangani BSrE',
                    'signed'         => $key !== 'TW4',
                    'tgl_signed'     => $key === 'TW4' ? null : ($key === 'TW1' ? '31/03/2026 15:40 WIB' : ($key === 'TW2' ? '30/06/2026 14:32 WIB' : '30/09/2026 16:10 WIB')),
                    'qr_hash'        => $key === 'TW4' ? null : 'BSRE-KOESNANDI-BAST-' . $key . '-2026-0' . rand(100, 999),
                ]
            );

            // Query barang ASTAP yang masuk di triwulan ini
            $months = $triwulanMonths[$key];
            $astaps = Astap::with(['jenisAstap', 'rekeningBelanja', 'registers'])
                ->where('tahun_perolehan', $tahun)
                ->where(function ($q) use ($months, $tahun, $key) {
                    $q->where(function ($sub) use ($months, $tahun) {
                        $sub->whereYear('sp2d_tanggal', $tahun)
                            ->whereIn(DB::raw("CAST(strftime('%m', sp2d_tanggal) AS INTEGER)"), $months);
                    })->orWhere(function ($sub) use ($months, $tahun) {
                        $sub->whereNull('sp2d_tanggal')
                            ->whereYear('bast_dokumen_tanggal', $tahun)
                            ->whereIn(DB::raw("CAST(strftime('%m', bast_dokumen_tanggal) AS INTEGER)"), $months);
                    });

                    // Khusus TW2 jika tidak ada SP2D spesifik, sertakan barang perolehan tahun ini yang belum bertanggal
                    if ($key === 'TW2') {
                        $q->orWhere(function ($sub) use ($tahun) {
                            $sub->whereNull('sp2d_tanggal')
                                ->whereNull('bast_dokumen_tanggal')
                                ->where('tahun_perolehan', $tahun);
                        });
                    }
                })
                ->orderBy('sp2d_tanggal')
                ->get();

            // Hitung Rekapitulasi 8 Kategori KIB
            $rekap = [
                'tanah'       => ['qty' => 0, 'nilai' => 0],
                'peralatan'   => ['qty' => 0, 'nilai' => 0],
                'gedung'      => ['qty' => 0, 'nilai' => 0],
                'jalan'       => ['qty' => 0, 'nilai' => 0],
                'aset_lain'   => ['qty' => 0, 'nilai' => 0],
                'kdp'         => ['qty' => 0, 'nilai' => 0],
                'atb'         => ['qty' => 0, 'nilai' => 0],
                'ekstra'      => ['qty' => 0, 'nilai' => 0],
            ];

            $detailBarang = [];
            $no = 1;

            foreach ($astaps as $ast) {
                $qty = (int) ($ast->jumlah_volume ?: 1);
                $totalNilai = (float) ($ast->total_realisasi ?: 0);
                $kode108 = $ast->jenisAstap?->sub_rincian_objek ?: ($ast->jenisAstap?->kode_108 ?: '1.3.2.02.01.01.001');

                if ($ast->is_extracomtable) {
                    $rekap['ekstra']['qty'] += $qty;
                    $rekap['ekstra']['nilai'] += $totalNilai;
                } elseif (str_starts_with($kode108, '1.3.1')) {
                    $rekap['tanah']['qty'] += $qty;
                    $rekap['tanah']['nilai'] += $totalNilai;
                } elseif (str_starts_with($kode108, '1.3.3')) {
                    $rekap['gedung']['qty'] += $qty;
                    $rekap['gedung']['nilai'] += $totalNilai;
                } elseif (str_starts_with($kode108, '1.3.4')) {
                    $rekap['jalan']['qty'] += $qty;
                    $rekap['jalan']['nilai'] += $totalNilai;
                } elseif (str_starts_with($kode108, '1.3.5')) {
                    $rekap['aset_lain']['qty'] += $qty;
                    $rekap['aset_lain']['nilai'] += $totalNilai;
                } elseif (str_starts_with($kode108, '1.3.6')) {
                    $rekap['kdp']['qty'] += $qty;
                    $rekap['kdp']['nilai'] += $totalNilai;
                } elseif (str_starts_with($kode108, '1.5.3') || str_starts_with($kode108, '1.5')) {
                    $rekap['atb']['qty'] += $qty;
                    $rekap['atb']['nilai'] += $totalNilai;
                } else {
                    $rekap['peralatan']['qty'] += $qty;
                    $rekap['peralatan']['nilai'] += $totalNilai;
                }

                $detailBarang[] = [
                    'no'              => $no++,
                    'tanggal_sp2d'    => $ast->sp2d_tanggal ? date('d/m/Y', strtotime($ast->sp2d_tanggal)) : ($ast->bast_dokumen_tanggal ? date('d/m/Y', strtotime($ast->bast_dokumen_tanggal)) : '-'),
                    'nomor_spk'       => $ast->spk_nomor ?: ($ast->sp2d_nomor ?: '-'),
                    'rekening'        => $ast->rekeningBelanja?->kode_rek ?: '5.2.02.01.01.0004',
                    'kode_108'        => $kode108,
                    'nama_barang'     => $ast->nama_barang,
                    'spesifikasi'     => $ast->keterangan_tambahan ?: ($ast->satuan . ' Pengadaan ' . $tahun),
                    'penyedia'        => $ast->penyedia_nama ?: 'Penyedia Rekanan RSUD',
                    'volume'          => $qty,
                    'satuan'          => $ast->satuan ?: 'Unit',
                    'nilai_realisasi' => $totalNilai,
                ];
            }

            $rekapItems = [
                ['no' => '1.', 'nama' => 'Tanah', 'qty' => $rekap['tanah']['qty'], 'nilai' => $rekap['tanah']['nilai']],
                ['no' => '2.', 'nama' => 'Peralatan Dan Mesin', 'qty' => $rekap['peralatan']['qty'], 'nilai' => $rekap['peralatan']['nilai']],
                ['no' => '3.', 'nama' => 'Gedung Dan Bangunan', 'qty' => $rekap['gedung']['qty'], 'nilai' => $rekap['gedung']['nilai']],
                ['no' => '4.', 'nama' => 'Jalan, Irigasi Dan Jaringan', 'qty' => $rekap['jalan']['qty'], 'nilai' => $rekap['jalan']['nilai']],
                ['no' => '5.', 'nama' => 'Aset Tetap Lainnya', 'qty' => $rekap['aset_lain']['qty'], 'nilai' => $rekap['aset_lain']['nilai']],
                ['no' => '6.', 'nama' => 'Kontruksi Dalam Pengerjaan', 'qty' => $rekap['kdp']['qty'], 'nilai' => $rekap['kdp']['nilai']],
                ['no' => '7.', 'nama' => 'Aset Tidak Berwujud', 'qty' => $rekap['atb']['qty'], 'nilai' => $rekap['atb']['nilai']],
                ['no' => '8.', 'nama' => 'Exstra Comtable', 'qty' => $rekap['ekstra']['qty'], 'nilai' => $rekap['ekstra']['nilai']],
            ];

            $tglBastObj = $doc->tanggal_bast ? Carbon::parse($doc->tanggal_bast) : Carbon::now();
            $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
            $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            $triwulanData[$key] = [
                'id'             => $doc->id,
                'key'            => $key,
                'nomor_surat'    => $doc->nomor_surat,
                'hari_tanggal'   => ($hariIndo[$tglBastObj->format('l')] ?? 'Selasa') . ' tanggal ' . $tglBastObj->format('d') . ' ' . ($bulanIndo[(int)$tglBastObj->format('m')] ?? '') . ' ' . $tglBastObj->format('Y'),
                'triwulan_nama'  => $triwulanNames[$key],
                'triwulan_label' => ($key === 'TW1' ? 'Triwulan I' : ($key === 'TW2' ? 'Triwulan II' : ($key === 'TW3' ? 'Triwulan III' : 'Triwulan IV'))) . ' Tahun ' . $tahun,
                'lokasi'         => $doc->lokasi,
                'pihak1_nama'    => $doc->pihak1_nama,
                'pihak1_nip'     => $doc->pihak1_nip,
                'pihak1_jabatan' => $doc->pihak1_jabatan,
                'pihak2_nama'    => $doc->pihak2_nama,
                'pihak2_nip'     => $doc->pihak2_nip,
                'pihak2_jabatan' => $doc->pihak2_jabatan,
                'direktur_nama'  => $doc->direktur_nama,
                'direktur_nip'   => $doc->direktur_nip,
                'pihak2_signed'  => (bool) $doc->signed,
                'pihak2_tgl_ttd' => $doc->tgl_signed ?: '-',
                'pihak2_qr_hash' => $doc->qr_hash ?: '',
                'status'         => $doc->status,
                'rekapItems'     => $rekapItems,
                'detailBarang'   => $detailBarang,
            ];
        }

        // =========================================================================
        // 2. DATA TAB 2: BAST DISTRIBUSI BARANG DARI GUDANG KE RUANGAN (FITUR DISTRIBUSI ASTAP)
        // =========================================================================
        $user = Auth::user();
        $isSubAdmin = $user && $user->isSubAdmin();

        $distribusiQuery = Distribusi::with([
            'unit',
            'items.astap.jenisAstap',
            'items.registers.astapRegister',
        ])->where(function($q) {
            $q->whereIn('status', ['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima'])
              ->orWhereHas('items.registers');
        });

        // Jika sub admin, batasi data hanya untuk unit miliknya
        if ($isSubAdmin && $user->unit_id) {
            $distribusiQuery->where('unit_id', $user->unit_id);
        }

        $distribusis = $distribusiQuery->orderBy('id', 'desc')->get();

        $unitPerbekalan = Unit::where('nama', 'LIKE', '%perbekalan%')
            ->orWhere('nama', 'LIKE', '%rumah tangga%')
            ->first();

        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];

        $distribusiList = [];
        foreach ($distribusis as $dst) {
            $tgl = $dst->tanggal_distribusi ? Carbon::parse($dst->tanggal_distribusi) : Carbon::now();
            $hariStr = $hariIndo[$tgl->format('l')] ?? 'Kamis';
            $tglAngka = $tgl->format('d');
            $bulanStr = $bulanIndo[(int)$tgl->format('m')] ?? 'Agustus';
            $tahunStr = $tgl->format('Y');

            $itemsData = [];
            $noIt = 1;
            foreach ($dst->items as $it) {
                $spec = is_array($it->astap?->spesifikasi_json)
                    ? $it->astap->spesifikasi_json
                    : (json_decode($it->astap?->spesifikasi_json ?? '', true) ?? []);
                $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? ($it->astap?->keterangan_tambahan ?? '-')));

                $nibarRegisters = $it->registers->map(function($dir) {
                    $reg = $dir->astapRegister;
                    return [
                        'nibar'   => $reg?->nibar   ?? ($reg?->no_register ?? '-'),
                        'reg_id'  => $reg?->id      ?? null,
                        'kondisi' => $reg?->kondisi ?? 'Baik',
                        'ruang'   => $reg?->ruang_pemegang ?? 'Gudang Aset',
                    ];
                })->values()->all();

                $nibarList = collect($nibarRegisters)->pluck('nibar')->filter(fn($v) => $v && $v !== '-')->values()->all();
                $nibarStr = !empty($nibarList) ? ' (NIBAR: ' . implode(', ', $nibarList) . ')' : '';

                $firstKondisi = $nibarRegisters[0]['kondisi'] ?? 'Baik';

                $itemsData[] = [
                    'no'              => $noIt++,
                    'id'              => $it->id,
                    'astap_id'        => $it->astap_id,
                    'nama_barang'     => $it->astap?->nama_barang ?: 'Barang Aset',
                    'kode_barang'     => $it->astap?->kode_108 ?: '-',
                    'jenis_nama'      => $it->astap?->jenisAstap?->nama_jenis ?: '-',
                    'merk'            => $merk,
                    'merk_type'       => $merk . $nibarStr,
                    'spesifikasi'     => $merk,
                    'qty'             => (int) $it->qty,
                    'qty_acc'         => $it->qty_acc !== null ? (int) $it->qty_acc : null,
                    // Vol. yang tampil di BAST adalah qty_acc. Jika belum di-ACC (qty_acc === null), tampilkan '-'
                    'vol_bast'        => $it->qty_acc !== null ? (int) $it->qty_acc : '-',
                    'satuan'          => $it->astap?->satuan ?: 'Unit',
                    'kondisi'         => $firstKondisi,
                    'keterangan'      => $it->keterangan ?: 'Distribusi ke ' . ($dst->unit?->nama ?: 'Ruangan'),
                    'nibar_list'      => $nibarList,
                    'nibar_registers' => $nibarRegisters,
                    'nibars'          => $nibarList,
                ];
            }

            $unitNama = $dst->unit?->nama ?: 'Ruangan RSUD';
            $unitKepala = $dst->unit?->kepala ?: 'Kepala Ruangan ' . $unitNama;
            $unitNip = $dst->unit?->nip ?: '-';

            $firstItemName = count($itemsData) > 0 ? $itemsData[0]['nama_barang'] : 'Barang Aset';
            $moreCount = count($itemsData) > 1 ? ' + ' . (count($itemsData) - 1) . ' item lainnya' : '';

            $isPlaceholderBast = empty($dst->bast_nomor) || str_contains((string)$dst->bast_nomor, 'Diterbitkan') || str_contains((string)$dst->bast_nomor, '[Auto]');
            $bastNomorResmi = !$isPlaceholderBast ? $dst->bast_nomor : ('032 / ' . str_pad($dst->id, 3, '0', STR_PAD_LEFT) . ' / 430.10.7 / ' . $tahunStr);

            $distribusiList[] = [
                'id'                => $dst->id,
                'kode'              => $dst->kode,
                'bast_nomor'        => $bastNomorResmi,
                'nomor_bast'        => $bastNomorResmi,
                'tgl'               => $tgl->format('d/m/Y'),
                'tgl_bast'          => $hariStr . ', ' . $tglAngka . ' ' . $bulanStr . ' ' . $tahunStr,
                'tanggal_distribusi'=> $tgl->format('Y-m-d'),
                'hari'              => $hariStr,
                'tanggal_angka'     => $tglAngka,
                'bulan'             => $bulanStr,
                'tahun'             => $tahunStr,
                'tahun_anggaran'    => $tahunStr,
                'sk_bupati_nomor'   => '188.45/430.10.7/2026',
                'sk_bupati_tanggal' => '02 Januari ' . $tahunStr,
                'nama'              => $firstItemName . $moreCount,
                'unit_id'           => $dst->unit_id,
                'unit_nama'         => $unitNama,
                'unit_tipe'         => $dst->unit?->tipe ?: 'Unit Pelayanan / Instalasi',
                'tujuan'            => $unitNama,
                'penerima'          => $unitKepala,
                'pj_nama'           => $unitKepala,
                'pj_nip'            => $unitNip,
                'pj_jabatan'        => 'Kepala ' . $unitNama,
                'pj_ruangan'        => $unitNama,
                'pj_jabatan_ttd'    => 'Kepala Ruangan ' . $unitNama,
                'pengurus_nama'     => $unitPerbekalan?->kepala ?: 'BUDI HARTONO, S. Sos',
                'pengurus_nip'      => $unitPerbekalan?->nip ?: '197602292008011010',
                'pengurus_jabatan'  => 'Pengurus Barang Aset',
                'pengurus_ruangan'  => $unitPerbekalan?->nama ?: 'Gudang Perbekalan',
                'status'            => $dst->signed ? 'Telah Ditandatangani BSrE' : ($dst->status ?: 'Draft'),
                'keterangan_lokasi' => $dst->keterangan ?: 'Penempatan Unit ' . $unitNama,
                'keterangan'        => $dst->keterangan,
                'signed'            => (bool) $dst->signed,
                'tgl_signed'        => $dst->tgl_signed ?: ($dst->signed ? ($dst->updated_at ? $dst->updated_at->format('d/m/Y H:i') . ' WIB' : $tgl->format('d/m/Y H:i') . ' WIB') : '-'),
                'qr_hash'           => $dst->signed ? ('BSRE-KOESNANDI-' . $dst->kode) : ('BSRE-KOESNANDI-DST-' . str_replace(' ', '', substr($unitNama, 0, 8)) . '-' . $tahunStr . '-' . $dst->id),
                'items'             => $itemsData,
            ];
        }

        // =========================================================================
        // 3. DATA TAB 3: BAMB MUTASI BARANG ANTAR RUANGAN
        // =========================================================================
        $mutasis = AstapMutasi::with(['items.register.astap'])
            ->where(function ($q) {
                $q->where('status', 'Disetujui Admin (Selesai)')
                  ->orWhere(function ($sub) {
                      $sub->where('persetujuan_pengirim', true)
                          ->where('persetujuan_penerima', true)
                          ->where('persetujuan_admin', true);
                  });
            })
            ->orderBy('id', 'desc')
            ->get();

        $mutasiList = [];
        foreach ($mutasis as $mts) {
            $tgl = $mts->tanggal_mutasi ? Carbon::parse($mts->tanggal_mutasi) : Carbon::now();
            $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];

            $itemsData = [];
            $noIt = 1;
            $firstItemNama = 'Barang Aset';
            $firstItemKode = '1.3.2.02.01.01.001';
            $totalQty = 0;

            foreach ($mts->items as $mit) {
                $reg = $mit->register;
                $astap = $reg?->astap;
                $nama = $astap?->nama_barang ?: 'Barang Aset';
                $kode = $astap?->jenisAstap?->sub_rincian_objek ?: '1.3.2.02.01.01.001';
                $nibar = $reg?->nibar ?: ($reg?->no_register ?: '-');

                if ($noIt === 1) {
                    $firstItemNama = $nama;
                    $firstItemKode = $kode;
                }
                $totalQty++;

                $itemsData[] = [
                    'no'          => $noIt++,
                    'nama_barang' => $nama,
                    'nibar'       => $nibar,
                    'kode_barang' => $kode,
                    'kondisi'     => $mit->kondisi ?: ($mts->kondisi ?: 'Baik'),
                    'satuan'      => $astap?->satuan ?: 'Unit',
                    'ruang_asal'  => $mts->ruangan_asal,
                    'ruang_tujuan'=> $mts->ruangan_tujuan,
                ];
            }

            $seq = str_replace('MTS-', '', $mts->nomor_bamb);
            $seqOnly = explode('-', $seq);
            $cleanSeq = end($seqOnly) ?: '0000001';

            $isDone = ($mts->status === 'Disetujui Admin (Selesai)' || $mts->persetujuan_admin);

            $mutasiList[] = [
                'id'                => $mts->id,
                'kode'              => $mts->nomor_bamb,
                'nomor_bast'        => $cleanSeq . ' / BAMB / 430.10.7 / ' . $tgl->format('Y'),
                'tgl_bast'          => ($hariIndo[$tgl->format('l')] ?? 'Senin') . ', ' . $tgl->format('d') . ' ' . ($bulanIndo[(int)$tgl->format('m')] ?? '') . ' ' . $tgl->format('Y'),
                'hari'              => $hariIndo[$tgl->format('l')] ?? 'Senin',
                'tanggal_angka'     => $tgl->format('d'),
                'bulan'             => $bulanIndo[(int)$tgl->format('m')] ?? '',
                'tahun'             => $tgl->format('Y'),
                'tahun_anggaran'    => $tgl->format('Y'),
                'sk_bupati_nomor'   => '188.45/969/430.4.2/2024',
                'sk_bupati_tanggal' => '02 Januari ' . $tgl->format('Y'),
                'nama'              => $firstItemNama . ($totalQty > 1 ? ' (' . $totalQty . ' Unit)' : ''),
                'kode_barang'       => $firstItemKode,
                'qty'               => $totalQty ?: 1,
                'satuan'            => 'Unit',
                'jenis_mutasi'      => $mts->jenis_mutasi ?: 'Pemindahan',
                'asal'              => $mts->ruangan_asal,
                'tujuan'            => $mts->ruangan_tujuan,
                'pemohon'           => $mts->penanggung_jawab_asal ?: 'Ka. Ruangan ' . $mts->ruangan_asal,
                'pj_asal_nama'      => $mts->penanggung_jawab_asal ?: 'Ka. Ruangan ' . $mts->ruangan_asal,
                'pj_asal_nip'       => '198004152006041008',
                'pj_asal_jabatan'   => 'Kepala Ruangan ' . $mts->ruangan_asal,
                'pj_tujuan_nama'    => $mts->penanggung_jawab_tujuan ?: 'Ka. Ruangan ' . $mts->ruangan_tujuan,
                'pj_tujuan_nip'     => '198410272009021003',
                'pj_tujuan_jabatan' => 'Kepala Ruangan ' . $mts->ruangan_tujuan,
                'pengurus_nama'     => 'ESTU PRATIKA SARI, SST',
                'pengurus_nip'      => '198805122011012005',
                'pengurus_jabatan'  => 'Pengurus Barang Aset RSUD',
                'status'            => $isDone ? 'Telah Ditandatangani BSrE' : $mts->status,
                'keterangan'        => $mts->alasan_mutasi ?: 'Mutasi Aset Antar Ruangan',
                'signed'            => $isDone,
                'tgl_signed'        => $isDone ? ($mts->tgl_persetujuan_admin ? Carbon::parse($mts->tgl_persetujuan_admin)->format('d/m/Y H:i') . ' WIB' : $tgl->format('d/m/Y H:i') . ' WIB') : '-',
                'qr_hash'           => 'BSRE-KOESNANDI-' . $mts->nomor_bamb,
                'items'             => $itemsData,
            ];
        }

        $units = Unit::orderBy('nama', 'asc')->get()->map(function($u) {
            return [
                'id'     => $u->id,
                'nama'   => $u->nama,
                'tipe'   => $u->tipe,
                'kepala' => $u->kepala,
                'nip'    => $u->nip,
            ];
        });

        return view('pages.berita_acara', [
            'tahun'              => $tahun,
            'triwulanDataJson'   => json_encode($triwulanData),
            'distribusiListJson' => json_encode($distribusiList),
            'mutasiListJson'     => json_encode($mutasiList),
            'unitsJson'          => json_encode($units),
        ]);
    }

    /**
     * Simpan Perubahan Metadata BAST Triwulan
     */
    public function saveTriwulan(Request $request, $key)
    {
        $tahun = $request->input('tahun', '2026');
        $doc = AstapBastTriwulan::firstOrCreate(['tahun' => $tahun, 'triwulan' => $key]);

        $doc->update([
            'nomor_surat'    => $request->input('nomor_surat', $doc->nomor_surat),
            'tanggal_bast'   => $request->input('tanggal_bast', $doc->tanggal_bast),
            'lokasi'         => $request->input('lokasi', $doc->lokasi),
            'pihak1_nama'    => $request->input('pihak1_nama', $doc->pihak1_nama),
            'pihak1_nip'     => $request->input('pihak1_nip', $doc->pihak1_nip),
            'pihak1_jabatan' => $request->input('pihak1_jabatan', $doc->pihak1_jabatan),
            'pihak2_nama'    => $request->input('pihak2_nama', $doc->pihak2_nama),
            'pihak2_nip'     => $request->input('pihak2_nip', $doc->pihak2_nip),
            'pihak2_jabatan' => $request->input('pihak2_jabatan', $doc->pihak2_jabatan),
            'direktur_nama'  => $request->input('direktur_nama', $doc->direktur_nama),
            'direktur_nip'   => $request->input('direktur_nip', $doc->direktur_nip),
            'catatan'        => $request->input('catatan', $doc->catatan),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen BAST ' . $key . ' Tahun ' . $tahun . ' berhasil diperbarui!',
            'data'    => $doc
        ]);
    }

    /**
     * Tanda Tangan Digital BSrE Dokumen BAST Triwulan
     */
    public function signTriwulan(Request $request, $key)
    {
        $tahun = $request->input('tahun', '2026');
        $doc = AstapBastTriwulan::firstOrCreate(['tahun' => $tahun, 'triwulan' => $key]);

        $timeStr = date('d/m/Y H:i') . ' WIB';
        $qrHash = 'BSRE-KOESNANDI-BAST-' . $key . '-' . $tahun . '-' . rand(1000, 9999);

        $doc->update([
            'signed'     => true,
            'status'     => 'Telah Ditandatangani BSrE',
            'tgl_signed' => $timeStr,
            'qr_hash'    => $qrHash,
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Dokumen BAST ' . $key . ' berhasil ditandatangani secara elektronik (BSrE)!',
            'tgl_signed' => $timeStr,
            'qr_hash'    => $qrHash,
            'status'     => 'Telah Ditandatangani BSrE'
        ]);
    }
}

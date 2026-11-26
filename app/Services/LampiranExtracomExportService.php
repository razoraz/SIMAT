<?php

namespace App\Services;

use App\Models\Astap;
use App\Models\AstapBastTriwulan;
use App\Models\AstapRegister;
use App\Models\AstapReklas;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LampiranExtracomExportService
{
    private const FILL_HEADER_BLUE   = 'FFDFEBF7';
    private const FILL_NUMBER_GRAY   = 'FFEDEDED';

    private const NUMFMT_CURRENCY_COMMA = '#,##0.00_);(#,##0.00)';
    private const NUMFMT_CURRENCY_ACCT  = '_(* #,##0.00_);_(* \(#,##0.00\);_(* "-"_);_(@_)';
    private const NUMFMT_INT            = '#,##0';

    /**
     * Bangun objek Spreadsheet lengkap 2 sheet secara 100% programatik
     * sesuai spesifikasi "3. Lampiran Extracom RSUD-2026 _RSDK_2026.xlsx".
     */
    public function buildSpreadsheet(string $tahun = 'all', string $triwulan = 'all'): Spreadsheet
    {
        $yearLabel = ($tahun === 'all' || trim($tahun) === '') ? (string) date('Y') : (string) (int) $tahun;
        $twInt     = $this->parseTriwulanInt($triwulan);
        $twRomawi  = $twInt ? ([1 => 'TW I', 2 => 'TW II', 3 => 'TW III', 4 => 'TW IV'][$twInt] ?? null) : null;
        $signDate  = $this->resolveSignatureDate($yearLabel, $twInt);
        $signatories = $this->resolveSignatories($yearLabel, $twInt);

        $penambahanGroups = $this->fetchPenambahanData($tahun, $twInt);
        $penguranganRows  = $this->fetchPenguranganData($tahun, $twInt);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('SIMAT-RK RSUD dr. H. Koesnandi')
            ->setTitle("Lampiran Extracom RSUD-{$yearLabel}_RSDK_{$yearLabel}")
            ->setSubject('Daftar Penambahan & Pengurangan Barang Ekstrakomptabel');

        // Sheet 1: 1. Daftar Penambahan ExtraC RSU
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('1. Daftar Penambahan ExtraC RSU');
        $this->buildSheetPenambahan($sheet1, $penambahanGroups, $yearLabel, $twRomawi, $signDate, $signatories);

        // Sheet 2: 2. Daftar Pengurangan Extra RSU
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('2. Daftar Pengurangan Extra RSU');
        $this->buildSheetPengurangan($sheet2, $penguranganRows, $yearLabel, $twRomawi, $signDate, $signatories);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Parse input triwulan ('TW I', 'TW II', '1'..'4', 'all') ke integer 1..4 atau null.
     */
    public function parseTriwulanInt(?string $triwulan): ?int
    {
        if ($triwulan === null) {
            return null;
        }
        $norm = strtoupper(preg_replace('/[\s_\-]+/', '', trim($triwulan)));
        if ($norm === '' || $norm === 'ALL' || $norm === 'TAHUNAN') {
            return null;
        }

        return match ($norm) {
            '1', 'TWI', 'TW1', 'TRIWULANI', 'TRIWULAN1', 'Q1', 'I'   => 1,
            '2', 'TWII', 'TW2', 'TRIWULANII', 'TRIWULAN2', 'Q2', 'II' => 2,
            '3', 'TWIII', 'TW3', 'TRIWULANIII', 'TRIWULAN3', 'Q3', 'III' => 3,
            '4', 'TWIV', 'TW4', 'TRIWULANIV', 'TRIWULAN4', 'Q4', 'IV' => 4,
            default => null,
        };
    }

    /**
     * Ambil data ASTAP yang sudah direklasifikasi menjadi Extracomptabel (Sheet 1).
     * Dikelompokkan berdasarkan sub-header jenis mutasi asal -> Extracom.
     */
    private function fetchPenambahanData(string $tahun, ?int $twInt): array
    {
        $reklasQuery = AstapReklas::active()
            ->where(function ($q) {
                $q->where('jenis_reklas', 'EKSTRAKOMPTABEL')
                  ->orWhere('tujuan_kib', 'EKSTRAKOMPTABEL');
            })
            ->with([
                'astap' => function ($q) {
                    $q->with([
                        'jenisAstap',
                        'belanjaModal.rekeningBelanja',
                        'belanjaModal.jenisPengadaan',
                        'unit',
                        'registers' => function ($rq) {
                            $rq->where('is_deleted', 0)->orderBy('no_register_int')->orderBy('id');
                        },
                        'registers.unit',
                    ]);
                },
            ])
            ->orderBy('tanggal_reklas', 'asc')
            ->orderBy('id', 'asc');

        if ($tahun !== 'all' && is_numeric($tahun)) {
            $reklasQuery->where('tahun', (int) $tahun);
        }
        if ($twInt !== null) {
            $reklasQuery->where('triwulan', $twInt);
        }

        $reklasRows = $reklasQuery->get();
        $coveredAstapIds = $reklasRows->pluck('astap_id')->filter()->unique()->values()->all();

        // Fallback aman bila ada ASTAP bertanda is_reklas=1 & jenis_reklas=EKSTRAKOMPTABEL tanpa row astap_reklasis
        $fallbackQuery = Astap::where('is_deleted', 0)
            ->where('is_reklas', true)
            ->where(function ($q) {
                $q->where('jenis_reklas', 'EKSTRAKOMPTABEL')
                  ->orWhere(function ($q2) {
                      $q2->where('is_extracomtable', true)->whereNotNull('jenis_reklas');
                  });
            })
            ->where('jenis_reklas', 'EKSTRAKOMPTABEL')
            ->when(!empty($coveredAstapIds), fn ($q) => $q->whereNotIn('id', $coveredAstapIds))
            ->with([
                'jenisAstap',
                'belanjaModal.rekeningBelanja',
                'belanjaModal.jenisPengadaan',
                'unit',
                'registers' => function ($rq) {
                    $rq->where('is_deleted', 0)->orderBy('no_register_int')->orderBy('id');
                },
                'registers.unit',
            ])
            ->orderBy('id', 'asc');

        if ($tahun !== 'all' && is_numeric($tahun)) {
            $fallbackQuery->where('tahun_perolehan', (int) $tahun);
        }
        if ($twInt !== null) {
            $fallbackQuery->whereIn('triwulan', $this->twVariantStrings($twInt));
        }

        $fallbackAstaps = $fallbackQuery->get();

        $entries = collect();

        foreach ($reklasRows as $reklas) {
            $astap = $reklas->astap;
            if (!$astap || (int) $astap->is_deleted === 1) {
                continue;
            }
            $groupTitle = $this->resolvePenambahanGroupTitle($reklas, $astap);
            $entries->push([
                'group'  => $groupTitle,
                'astap'  => $astap,
                'reklas' => $reklas,
            ]);
        }

        foreach ($fallbackAstaps as $astap) {
            $entries->push([
                'group'  => 'Reklas dari Aset Tetap Ke Extracom',
                'astap'  => $astap,
                'reklas' => null,
            ]);
        }

        if ($entries->isEmpty()) {
            return [];
        }

        $grouped = [];
        foreach ($entries as $entry) {
            $groupName = $entry['group'];
            $astap     = $entry['astap'];
            $reklas    = $entry['reklas'];
            $spec      = $this->decodeSpec($astap->spesifikasi_json);
            $specMeta  = $this->extractSpecAttributes($spec);

            $bm      = $astap->belanjaModal;
            $rb      = $bm?->rekeningBelanja;
            $jp      = $bm?->jenisPengadaan;
            $ja      = $astap->jenisAstap;
            $kodeRek = $rb?->kode_rek ?: ($jp?->sub_kegiatan_kode ?: '');
            $rincian = $rb?->nama_belanja ?: ($reklas?->asal_nama ?: '');

            $kode108 = $this->firstNonEmpty(
                $astap->kode_108,
                $ja?->sub_sub_rincian_objek,
                $ja?->sub_rincian_objek,
                $ja?->jenis,
                $reklas?->asal_kode,
                $spec['mesin_kode_barang'] ?? null,
                $spec['lainnya_kode_barang'] ?? null
            ) ?? '-';

            $namaBarang = $this->firstNonEmpty(
                $astap->nama_barang,
                $ja?->uraian_sub_sub_rincian,
                $reklas?->asal_nama
            ) ?? '-';

            $volumeMaster = max(1, (int) ($astap->jumlah_volume ?: 1));
            $anggaran     = (float) ($astap->jumlah_anggaran ?: 0);
            $realisasi    = (float) ($astap->total_realisasi ?: ($reklas?->nilai_reklas ?: 0));
            $hargaSatuan  = (float) ($astap->harga_satuan ?: ($volumeMaster > 0 ? $realisasi / $volumeMaster : $realisasi));
            $adminTotal   = (float) ($astap->biaya_administrasi_proyek ?: ($spec['admin_proyek'] ?? 0));
            $adminPerUnit = $volumeMaster > 0 ? ($adminTotal / $volumeMaster) : 0.0;

            $asalPerolehan = $this->resolveAsalPerolehan($astap, $spec);
            $keterangan    = $this->buildPenambahanKeterangan($reklas, $astap, $rb?->kode_rek, $rb?->nama_belanja);

            $registers = $astap->registers ?? collect();
            $itemRows  = [];

            if ($registers->isNotEmpty()) {
                $regCount = $registers->count();
                $adminPerReg = $regCount > 0 ? ($adminTotal / $regCount) : 0.0;

                foreach ($registers as $regIdx => $reg) {
                    $regSpec  = $this->pickRepeaterItemForIndex($spec, $regIdx);
                    $noPabrik = $this->firstNonEmpty(
                        $regSpec['mesin_no_pabrik'] ?? null,
                        $regSpec['mesin_no_rangka'] ?? null,
                        $regSpec['mesin_no_mesin'] ?? null,
                        $specMeta['no_pabrik'] !== '-' ? $specMeta['no_pabrik'] : null,
                        $reg->nibar,
                        $reg->no_register
                    ) ?? '-';

                    $lokasi = $this->firstNonEmpty(
                        $reg->ruang_pemegang,
                        $reg->unit?->nama,
                        $astap->unit?->nama,
                        $astap->alamat_barang
                    ) ?? '-';

                    $itemRows[] = [
                        'is_first'     => $regIdx === 0,
                        'kode_rek'     => $regIdx === 0 ? (string) $kodeRek : '',
                        'rincian'      => $regIdx === 0 ? (string) $rincian : '',
                        'anggaran'     => $regIdx === 0 ? $anggaran : null,
                        'realisasi'    => $regIdx === 0 ? $realisasi : null,
                        'kode_108'     => (string) $kode108,
                        'nama_barang'  => (string) $namaBarang,
                        'merk'         => (string) ($this->firstNonEmpty($regSpec['mesin_merk'] ?? null, $specMeta['merk']) ?? '-'),
                        'type'         => (string) ($this->firstNonEmpty($regSpec['mesin_type'] ?? null, $specMeta['type']) ?? '-'),
                        'no_pabrik'    => (string) $noPabrik,
                        'volume'       => 1,
                        'satuan'       => (string) ($astap->satuan ?: 'Unit'),
                        'nilai_satuan' => $hargaSatuan,
                        'biaya_admin'  => round($adminPerReg, 2),
                        'asal'         => $asalPerolehan,
                        'lokasi'       => (string) $lokasi,
                        'keterangan'   => $keterangan,
                    ];
                }
            } else {
                $noPabrik = $this->firstNonEmpty(
                    $specMeta['no_pabrik'] !== '-' ? $specMeta['no_pabrik'] : null,
                    $specMeta['no_rangka'] !== '-' ? $specMeta['no_rangka'] : null,
                    $specMeta['no_mesin'] !== '-' ? $specMeta['no_mesin'] : null
                ) ?? '-';

                $lokasi = $this->firstNonEmpty(
                    $astap->unit?->nama,
                    $astap->alamat_barang,
                    $spec['ruang_pemegang'] ?? null
                ) ?? '-';

                $itemRows[] = [
                    'is_first'     => true,
                    'kode_rek'     => (string) $kodeRek,
                    'rincian'      => (string) $rincian,
                    'anggaran'     => $anggaran,
                    'realisasi'    => $realisasi,
                    'kode_108'     => (string) $kode108,
                    'nama_barang'  => (string) $namaBarang,
                    'merk'         => (string) $specMeta['merk'],
                    'type'         => (string) $specMeta['type'],
                    'no_pabrik'    => (string) $noPabrik,
                    'volume'       => $volumeMaster,
                    'satuan'       => (string) ($astap->satuan ?: 'Unit'),
                    'nilai_satuan' => $hargaSatuan,
                    'biaya_admin'  => round($adminPerUnit * $volumeMaster, 2),
                    'asal'         => $asalPerolehan,
                    'lokasi'       => (string) $lokasi,
                    'keterangan'   => $keterangan,
                ];
            }

            if (!isset($grouped[$groupName])) {
                $grouped[$groupName] = [];
            }
            $grouped[$groupName][] = $itemRows;
        }

        return $grouped;
    }

    /**
     * Ambil data pengurangan Extracom untuk Sheet 2:
     * hanya aset yang pernah direklasifikasi menjadi Extracom (atau berasal dari reklas Extracom)
     * yang dihapus (is_deleted = 1) atau direklasifikasi keluar dari Extracom (Kapitalisasi Intrakom / Hibah / Mutasi Eksternal).
     */
    private function fetchPenguranganData(string $tahun, ?int $twInt): array
    {
        $rows = [];
        $reklasToExtracomAstapIds = AstapReklas::active()
            ->where(function ($q) {
                $q->where('jenis_reklas', 'EKSTRAKOMPTABEL')
                  ->orWhere('tujuan_kib', 'EKSTRAKOMPTABEL');
            })
            ->pluck('astap_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // 1. Transaksi reklas KELUAR dari Extracom (Kapitalisasi Intrakom / Hibah Keluar / Mutasi Keluar OPD pada aset hasil reklas Extracom)
        $outReklasQuery = AstapReklas::active()
            ->where(function ($q) use ($reklasToExtracomAstapIds) {
                $q->where('jenis_reklas', 'KAPITALISASI_INTRAKOM')
                  ->orWhere('asal_kib', 'EKSTRAKOMPTABEL')
                  ->orWhere('asal_kode', 'KOR_EXTRACOM');

                if (!empty($reklasToExtracomAstapIds)) {
                    $q->orWhere(function ($q2) use ($reklasToExtracomAstapIds) {
                        $q2->whereIn('astap_id', $reklasToExtracomAstapIds)
                           ->whereIn('jenis_reklas', ['HIBAH_KELUAR', 'MUTASI_EKSTERNAL']);
                    });
                }
            })
            ->with(['astap.jenisAstap', 'astap.registers'])
            ->orderBy('tanggal_reklas', 'asc')
            ->orderBy('id', 'asc');

        if ($tahun !== 'all' && is_numeric($tahun)) {
            $outReklasQuery->where('tahun', (int) $tahun);
        }
        if ($twInt !== null) {
            $outReklasQuery->where('triwulan', $twInt);
        }

        $outReklas = $outReklasQuery->get();
        $handledAstapOutIds = [];

        foreach ($outReklas as $reklas) {
            $astap = $reklas->astap;
            if (!$astap) {
                continue;
            }
            $handledAstapOutIds[] = $astap->id;
            $rows[] = $this->formatPenguranganRow(
                $astap,
                $astap->registers?->first(),
                (int) ($astap->jumlah_volume ?: 1),
                (float) ($reklas->nilai_reklas ?: $astap->total_realisasi ?: 0),
                trim(implode(' · ', array_filter([
                    $reklas->jenis_reklas === 'KAPITALISASI_INTRAKOM'
                        ? 'Kapitalisasi ke Intrakomptabel (' . ($reklas->tujuan_kib ?: 'KIB') . ')'
                        : str_replace('_', ' ', (string) $reklas->jenis_reklas),
                    $reklas->nomor_ba_reklas ? 'BA: ' . $reklas->nomor_ba_reklas : null,
                    $reklas->alasan_reklas ?: $reklas->keterangan,
                ]))) ?: '-'
            );
        }

        // 2. Aset Master hasil reklas Extracom yang dihapus (is_deleted = 1)
        $deletedAstaps = Astap::where('is_deleted', 1)
            ->where(function ($q) use ($reklasToExtracomAstapIds) {
                if (!empty($reklasToExtracomAstapIds)) {
                    $q->whereIn('id', $reklasToExtracomAstapIds);
                }
                $q->orWhere(function ($q2) {
                    $q2->where('is_reklas', true)
                       ->where('jenis_reklas', 'EKSTRAKOMPTABEL');
                });
            })
            ->when(!empty($handledAstapOutIds), fn ($q) => $q->whereNotIn('id', $handledAstapOutIds))
            ->with(['jenisAstap', 'registers'])
            ->orderBy('deleted_at', 'asc')
            ->get();

        foreach ($deletedAstaps as $astap) {
            if (!$this->matchesDeletedPeriod($astap->deleted_at, $astap->tahun_perolehan, $astap->triwulan, $tahun, $twInt)) {
                continue;
            }
            $regs = $astap->registers ?? collect();
            if ($regs->isNotEmpty()) {
                $hargaPerUnit = (float) ($astap->harga_satuan ?: ($regs->count() > 0 ? ((float) $astap->total_realisasi / $regs->count()) : 0));
                foreach ($regs as $reg) {
                    $rows[] = $this->formatPenguranganRow(
                        $astap,
                        $reg,
                        1,
                        $hargaPerUnit,
                        $astap->keterangan_tambahan ?: ($astap->deleted_by ? 'Dihapus (' . $astap->deleted_by . ')' : 'Penghapusan Barang Ekstrakomptabel')
                    );
                }
            } else {
                $rows[] = $this->formatPenguranganRow(
                    $astap,
                    null,
                    max(1, (int) ($astap->jumlah_volume ?: 1)),
                    (float) ($astap->total_realisasi ?: 0),
                    $astap->keterangan_tambahan ?: ($astap->deleted_by ? 'Dihapus (' . $astap->deleted_by . ')' : 'Penghapusan Barang Ekstrakomptabel')
                );
            }
        }

        // 3. Register NIBAR satuan yang dihapus (is_deleted = 1) dari aset hasil reklas Extracom yang masih aktif
        $deletedRegs = AstapRegister::where('is_deleted', 1)
            ->whereHas('astap', function ($q) use ($reklasToExtracomAstapIds) {
                $q->where('is_deleted', 0)
                  ->where(function ($sub) use ($reklasToExtracomAstapIds) {
                      if (!empty($reklasToExtracomAstapIds)) {
                          $sub->whereIn('id', $reklasToExtracomAstapIds);
                      }
                      $sub->orWhere(function ($q2) {
                          $q2->where('is_reklas', true)
                             ->where('jenis_reklas', 'EKSTRAKOMPTABEL');
                      });
                  });
            })
            ->with(['astap.jenisAstap'])
            ->orderBy('deleted_at', 'asc')
            ->get();

        foreach ($deletedRegs as $reg) {
            $astap = $reg->astap;
            if (!$astap) {
                continue;
            }
            if (!$this->matchesDeletedPeriod($reg->deleted_at, $reg->tahun_perolehan ?: $astap->tahun_perolehan, $astap->triwulan, $tahun, $twInt)) {
                continue;
            }
            $unitPrice = (float) ($astap->harga_satuan ?: ($astap->jumlah_volume > 0 ? ($astap->total_realisasi / $astap->jumlah_volume) : 0));
            $rows[] = $this->formatPenguranganRow(
                $astap,
                $reg,
                1,
                $unitPrice,
                $reg->deleted_by ? 'Register dihapus (' . $reg->deleted_by . ')' : ($astap->keterangan_tambahan ?: 'Penghapusan Barang Ekstrakomptabel')
            );
        }

        return $rows;
    }

    private function formatPenguranganRow(Astap $astap, ?AstapRegister $reg, int $qty, float $nilai, string $keterangan): array
    {
        $spec     = $this->decodeSpec($astap->spesifikasi_json);
        $specMeta = $this->extractSpecAttributes($spec);
        $ja       = $astap->jenisAstap;

        $kode108 = $this->firstNonEmpty(
            $astap->kode_108,
            $ja?->sub_sub_rincian_objek,
            $ja?->sub_rincian_objek,
            $ja?->jenis
        ) ?? '-';

        $regVal = $reg ? ($reg->nibar ?: $reg->no_register ?: '-') : '-';

        $merkTypeParts = array_filter([
            $specMeta['merk'] !== '-' ? $specMeta['merk'] : null,
            $specMeta['type'] !== '-' ? $specMeta['type'] : null,
        ]);

        $identParts = array_filter([
            $specMeta['sertifikat_nomor'] !== '-' ? $specMeta['sertifikat_nomor'] : null,
            $specMeta['no_pabrik'] !== '-' ? $specMeta['no_pabrik'] : null,
            $specMeta['no_rangka'] !== '-' ? $specMeta['no_rangka'] : null,
            $specMeta['no_mesin'] !== '-' ? $specMeta['no_mesin'] : null,
        ]);

        $rawKondisi = strtoupper(trim((string) ($reg?->kondisi ?: ($spec['kondisi'] ?? 'RB'))));
        $kondisiCode = match (true) {
            str_contains($rawKondisi, 'KURANG') || $rawKondisi === 'KB' => 'KB',
            str_contains($rawKondisi, 'RUSAK') || $rawKondisi === 'RB'  => 'RB',
            str_contains($rawKondisi, 'BAIK') || $rawKondisi === 'B'    => 'B',
            default => $rawKondisi ?: 'RB',
        };

        return [
            'kode_barang'    => (string) $kode108,
            'register'       => (string) $regVal,
            'nama_barang'    => (string) ($astap->nama_barang ?: ($ja?->uraian_sub_sub_rincian ?: '-')),
            'merk_type'      => !empty($merkTypeParts) ? implode(' / ', $merkTypeParts) : '-',
            'nomor_identitas'=> !empty($identParts) ? implode(' / ', $identParts) : '-',
            'bahan'          => (string) $specMeta['bahan'],
            'asal'           => $this->resolveAsalPerolehan($astap, $spec),
            'tahun'          => (string) ($astap->tahun_perolehan ?: '-'),
            'satuan'         => (string) ($astap->satuan ?: 'Unit'),
            'kondisi'        => $kondisiCode,
            'jumlah'         => $qty,
            'nilai'          => $nilai,
            'keterangan'     => $keterangan ?: '-',
        ];
    }

    /**
     * Render Sheet 1: 1. Daftar Penambahan ExtraC RSU (Kolom A..R).
     */
    private function buildSheetPenambahan(
        Worksheet $sheet,
        array $groups,
        string $yearLabel,
        ?string $twRomawi,
        string $signDate,
        array $signatories
    ): void {
        $colWidths = [
            'A' => 4.5,   'B' => 11.0,  'C' => 16.0,  'D' => 14.0,  'E' => 14.5,
            'F' => 17.5,  'G' => 20.0,  'H' => 17.0,  'I' => 13.0,  'J' => 14.0,
            'K' => 8.0,   'L' => 8.5,   'M' => 14.0,  'N' => 12.0,  'O' => 15.0,
            'P' => 13.0,  'Q' => 14.0,  'R' => 28.0,
        ];
        foreach ($colWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Kop Baris 1-4
        $periodeTitle = $twRomawi
            ? "TAHUN ANGGARAN {$yearLabel} ({$twRomawi})"
            : "TAHUN ANGGARAN  {$yearLabel}";

        $kopLines = [
            1 => 'PEMERINTAH KABUPATEN BONDOWOSO',
            2 => 'RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI',
            3 => 'DAFTAR PENAMBAHAN BARANG EXTRA COMPTABLE',
            4 => $periodeTitle,
        ];
        foreach ($kopLines as $r => $text) {
            $sheet->setCellValue("A{$r}", $text);
            $sheet->mergeCells("A{$r}:R{$r}");
            $sheet->getStyle("A{$r}:R{$r}")->applyFromArray([
                'font'      => ['name' => 'Calibri', 'size' => 14, 'bold' => true, 'color' => ['argb' => 'FF000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(18.75);
        }

        // Header Tabel Bertingkat Baris 5-7
        $sheet->setCellValue('A5', 'NO');
        $sheet->setCellValue('B5', 'BELANJA MODAL');
        $sheet->setCellValue('F5', 'RINCIAN BARANG');

        $sheet->setCellValue('B6', "Kode Keg. / Kode Rek. Bel. Modal APBD {$yearLabel}");
        $sheet->setCellValue('C6', 'RINCIAN BELANJA MODAL');
        $sheet->setCellValue('D6', 'JUMLAH ANGGARAN (Rp)');
        $sheet->setCellValue('E6', 'JUMLAH REALISASI SP2D (Rp)');
        $sheet->setCellValue('F6', 'No Rek. Permendagri No. 108/2016');
        $sheet->setCellValue('G6', 'NAMA BARANG');
        $sheet->setCellValue('H6', 'MERK');
        $sheet->setCellValue('I6', 'TYPE');
        $sheet->setCellValue('J6', 'NO PABRIK/NO CHASIS/NO MESIN');
        $sheet->setCellValue('K6', 'VOLUME');
        $sheet->setCellValue('M6', 'NILAI SATUAN BARANG      (Rp)');
        $sheet->setCellValue('N6', 'BIAYA ADMINISTRASI PROYEK BARANG(Rp)');
        $sheet->setCellValue('O6', 'HARGA PEROLEHAN BARANG (Rp)');
        $sheet->setCellValue('P6', 'ASAL/CARA PEROLEHAN BARANG');
        $sheet->setCellValue('Q6', 'LOKASI BARANG');
        $sheet->setCellValue('R6', 'KETERANGAN');

        $sheet->setCellValue('K7', 'JUMLAH BARANG');
        $sheet->setCellValue('L7', 'NAMA SATUAN BARANG');

        $merges57 = [
            'A5:A7', 'B5:E5', 'F5:R5',
            'B6:B7', 'C6:C7', 'D6:D7', 'E6:E7',
            'F6:F7', 'G6:G7', 'H6:H7', 'I6:I7', 'J6:J7',
            'K6:L6',
            'M6:M7', 'N6:N7', 'O6:O7', 'P6:P7', 'Q6:Q7', 'R6:R7',
        ];
        foreach ($merges57 as $range) {
            $sheet->mergeCells($range);
        }

        $sheet->getStyle('A5:R7')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 9, 'bold' => true, 'color' => ['argb' => 'FF000000']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::FILL_HEADER_BLUE]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(18);
        $sheet->getRowDimension(6)->setRowHeight(14);
        $sheet->getRowDimension(7)->setRowHeight(54.75);

        // Baris 8: Penomoran Kolom 1 s.d. 18 (dengan O8 = "15=(13+14)")
        for ($c = 1; $c <= 18; $c++) {
            $colStr = Coordinate::stringFromColumnIndex($c);
            if ($c === 15) {
                $sheet->setCellValueExplicit("O8", '15=(13+14)', DataType::TYPE_STRING);
            } else {
                $sheet->setCellValue("{$colStr}8", $c);
            }
        }
        $sheet->getStyle('A8:R8')->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 9, 'bold' => false, 'color' => ['argb' => 'FF000000']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::FILL_NUMBER_GRAY]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'top'      => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'left'     => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'right'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'vertical' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'bottom'   => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(13.5);

        // Isi Data Tabel Langsung Mulai Baris 9
        $currentRow = 9;
        $dataStartRow = 9;
        $itemCounter = 1;

        if (empty($groups)) {
            $emptyRowCount = 9;
            for ($i = 0; $i < $emptyRowCount; $i++) {
                $this->stylePenambahanDataRow($sheet, $currentRow, [], $colWidths);
                $currentRow++;
            }
            $this->attachNihilWatermark($sheet, 'H12', 12, 18, 360, 82, 'Watermark NIHIL Penambahan');
        } else {
            foreach ($groups as $astapRowBatches) {
                foreach ($astapRowBatches as $regRows) {
                    foreach ($regRows as $idx => $row) {
                        if ($idx === 0) {
                            $sheet->setCellValue("A{$currentRow}", $itemCounter++);
                        }
                        if ($row['kode_rek'] !== '') {
                            $sheet->setCellValueExplicit("B{$currentRow}", $row['kode_rek'], DataType::TYPE_STRING);
                        }
                        if ($row['rincian'] !== '') {
                            $sheet->setCellValue("C{$currentRow}", $row['rincian']);
                        }
                        if ($row['anggaran'] !== null) {
                            $sheet->setCellValue("D{$currentRow}", $row['anggaran']);
                        }
                        if ($row['realisasi'] !== null) {
                            $sheet->setCellValue("E{$currentRow}", $row['realisasi']);
                        }

                        // Paksa kolom Kode Rekening (F) dan No Pabrik/Seri (J) menjadi tipe String/Text
                        $sheet->setCellValueExplicit("F{$currentRow}", $row['kode_108'], DataType::TYPE_STRING);
                        $sheet->setCellValue("G{$currentRow}", $row['nama_barang']);
                        $sheet->setCellValue("H{$currentRow}", $row['merk']);
                        $sheet->setCellValue("I{$currentRow}", $row['type']);
                        $sheet->setCellValueExplicit("J{$currentRow}", $row['no_pabrik'], DataType::TYPE_STRING);
                        $sheet->setCellValue("K{$currentRow}", $row['volume']);
                        $sheet->setCellValue("L{$currentRow}", $row['satuan']);
                        $sheet->setCellValue("M{$currentRow}", $row['nilai_satuan']);
                        $sheet->setCellValue("N{$currentRow}", $row['biaya_admin']);
                        $sheet->setCellValue("O{$currentRow}", "=(M{$currentRow}+N{$currentRow})*K{$currentRow}");
                        $sheet->setCellValue("P{$currentRow}", $row['asal']);
                        $sheet->setCellValue("Q{$currentRow}", $row['lokasi']);
                        $sheet->setCellValue("R{$currentRow}", $row['keterangan']);

                        $this->stylePenambahanDataRow($sheet, $currentRow, [
                            'C' => $row['rincian'],
                            'G' => $row['nama_barang'],
                            'H' => $row['merk'],
                            'J' => $row['no_pabrik'],
                            'Q' => $row['lokasi'],
                            'R' => $row['keterangan'],
                        ], $colWidths);
                        $currentRow++;
                    }
                }
            }
        }

        $dataEndRow = $currentRow - 1;
        $totalRow   = $currentRow;

        // Baris JUMLAH
        $sheet->setCellValue("B{$totalRow}", 'JUMLAH ');
        $sheet->setCellValue("K{$totalRow}", "=SUM(K{$dataStartRow}:K{$dataEndRow})");
        $sheet->setCellValue("O{$totalRow}", "=SUM(O{$dataStartRow}:O{$dataEndRow})");

        $sheet->getStyle("A{$totalRow}:R{$totalRow}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 9, 'bold' => true, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF000000']],
                'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'right'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getStyle("A{$totalRow}:J{$totalRow}")->getBorders()->getVertical()->setBorderStyle(Border::BORDER_NONE);
        $sheet->getStyle("B{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("K{$totalRow}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'numberFormat' => ['formatCode' => self::NUMFMT_INT],
            'borders'      => [
                'left'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getStyle("O{$totalRow}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => self::NUMFMT_CURRENCY_ACCT],
            'borders'      => [
                'left'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getRowDimension($totalRow)->setRowHeight(27.75);

        // Blok Tanda Tangan 2 Kolom Sejajar (Kiri A..F, Kanan Q..R)
        $sigStart = $totalRow + 2;
        $this->renderPenambahanSignatures($sheet, $sigStart, $signDate, $signatories);
        $lastRow = $sigStart + 6;

        // Page Setup & Print Area (Landscape, Fit Width 1, Repeat Rows 5-8)
        $ps = $sheet->getPageSetup();
        $ps->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $ps->setPaperSize(PageSetup::PAPERSIZE_FOLIO);
        $ps->setFitToPage(true);
        $ps->setFitToWidth(1);
        $ps->setFitToHeight(0);
        $ps->setRowsToRepeatAtTopByStartAndEnd(5, 8);
        $ps->setPrintArea("A1:R{$lastRow}");

        $pm = $sheet->getPageMargins();
        $pm->setTop(0.24)->setRight(0.24)->setBottom(0.24)->setLeft(0.47)->setHeader(0.51)->setFooter(0.51);
    }

    private function stylePenambahanDataRow(Worksheet $sheet, int $row, array $wrappedTexts, array $colWidths): void
    {
        $sheet->getStyle("A{$row}:R{$row}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 9, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => [
                'top'      => ['borderStyle' => Border::BORDER_DASHED, 'color' => ['argb' => 'FF000000']],
                'bottom'   => ['borderStyle' => Border::BORDER_DASHED, 'color' => ['argb' => 'FF000000']],
                'left'     => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'right'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'vertical' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);

        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle("D{$row}:E{$row}")->getNumberFormat()->setFormatCode(self::NUMFMT_CURRENCY_COMMA);
        $sheet->getStyle("D{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$row}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'numberFormat' => ['formatCode' => '@'],
        ]);
        $sheet->getStyle("G{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("J{$row}")->applyFromArray([
            'font'         => ['name' => 'Calibri', 'size' => 8],
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            'numberFormat' => ['formatCode' => '@'],
        ]);
        $sheet->getStyle("K{$row}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'numberFormat' => ['formatCode' => '0'],
        ]);
        $sheet->getStyle("L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("M{$row}:O{$row}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => self::NUMFMT_CURRENCY_COMMA],
        ]);
        $sheet->getStyle("P{$row}:Q{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("R{$row}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 8],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $height = $this->calculateDynamicRowHeight($wrappedTexts, $colWidths, 29.25, 13.0);
        $sheet->getRowDimension($row)->setRowHeight($height);
    }

    private function renderPenambahanSignatures(Worksheet $sheet, int $startRow, string $signDate, array $signatories): void
    {
        $r0 = $startRow;
        $r1 = $startRow + 1;
        $r2 = $startRow + 2;
        $r4 = $startRow + 4;
        $r5 = $startRow + 5;
        $r6 = $startRow + 6;

        $sheet->setCellValue("A{$r0}", 'MENGETAHUI,');
        $sheet->setCellValue("A{$r1}", 'DIREKTUR RSUD dr. H. KOESNADI BONDOWOSO');
        $sheet->setCellValue("A{$r4}", $signatories['direktur_nama']);
        $sheet->setCellValue("A{$r5}", $signatories['direktur_pangkat']);
        $sheet->setCellValue("A{$r6}", 'NIP. ' . $signatories['direktur_nip']);

        $sheet->mergeCells("A{$r0}:F{$r0}");
        $sheet->mergeCells("A{$r1}:F{$r2}");
        $sheet->mergeCells("A{$r4}:F{$r4}");
        $sheet->mergeCells("A{$r5}:F{$r5}");
        $sheet->mergeCells("A{$r6}:F{$r6}");

        $sheet->setCellValue("Q{$r0}", "Bondowoso, {$signDate}");
        $sheet->setCellValue("Q{$r1}", 'PENGURUS BARANG ASET');
        $sheet->setCellValue("Q{$r4}", $signatories['pengurus_nama']);
        $sheet->setCellValue("Q{$r5}", 'NIP. ' . $signatories['pengurus_nip']);

        $sheet->mergeCells("Q{$r0}:R{$r0}");
        $sheet->mergeCells("Q{$r1}:R{$r1}");
        $sheet->mergeCells("Q{$r4}:R{$r4}");
        $sheet->mergeCells("Q{$r5}:R{$r5}");

        $sheet->getStyle("A{$r0}:R{$r6}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 12, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getStyle("A{$r4}:R{$r4}")->getFont()->setBold(true);

        foreach ([$r0, $r1, $r2, $r4, $r5, $r6] as $r) {
            $sheet->getRowDimension($r)->setRowHeight(16.5);
        }
        $sheet->getRowDimension($startRow + 3)->setRowHeight(24.0);
    }

    /**
     * Render Sheet 2: 2. Daftar Pengurangan Extra RSU (Kolom A..N saja, bebas sisa catatan kolom O+).
     */
    private function buildSheetPengurangan(
        Worksheet $sheet,
        array $rows,
        string $yearLabel,
        ?string $twRomawi,
        string $signDate,
        array $signatories
    ): void {
        $colWidths = [
            'A' => 6.0,   'B' => 17.5,  'C' => 18.0,  'D' => 20.0,  'E' => 13.0,
            'F' => 16.0,  'G' => 11.5,  'H' => 13.5,  'I' => 13.5,  'J' => 9.5,
            'K' => 10.5,  'L' => 10.5,  'M' => 14.5,  'N' => 22.0,
        ];
        foreach ($colWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $periodeTitle = $twRomawi
            ? "TAHUN ANGGARAN {$yearLabel} ({$twRomawi})"
            : "TAHUN ANGGARAN {$yearLabel}";

        $kopLines = [
            1 => 'PEMERINTAH KABUPATEN BONDOWOSO',
            2 => 'RSUD  dr. H. KOESNADI  BONDOWOSO',
            3 => 'DAFTAR PENGURANGAN BARANG EXTRA COMPTABLE',
            4 => $periodeTitle,
        ];
        foreach ($kopLines as $r => $text) {
            $sheet->setCellValue("A{$r}", $text);
            $sheet->mergeCells("A{$r}:N{$r}");
            $sheet->getStyle("A{$r}:N{$r}")->applyFromArray([
                'font'      => ['name' => 'Arial', 'size' => 12, 'bold' => true, 'color' => ['argb' => 'FF000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(15.75);
        }

        // Header Tabel Baris 7-8
        $sheet->setCellValue('A7', 'No. Urut');
        $sheet->setCellValue('B7', 'Kode Barang');
        $sheet->setCellValue('C7', 'Register');
        $sheet->setCellValue('D7', 'Spesifikasi Barang');
        $sheet->setCellValue('G7', 'Bahan');
        $sheet->setCellValue('H7', 'Asal/Cara Perolehan');
        $sheet->setCellValue('I7', 'Tahun Beli/Perolehan');
        $sheet->setCellValue('J7', 'Satuan');
        $sheet->setCellValue('K7', 'Keadaan Barang (B/KB/RB)');
        $sheet->setCellValue('L7', 'Jumlah');
        $sheet->setCellValue('N7', 'Keterangan');

        $sheet->setCellValue('D8', 'Nama/Jenis Barang');
        $sheet->setCellValue('E8', 'Merk / Type');
        $sheet->setCellValue('F8', 'No.Sertifikat / No. Pabrik / No. Mesin');
        $sheet->setCellValue('L8', 'Barang');
        $sheet->setCellValue('M8', 'Nilai');

        $merges78 = [
            'A7:A8', 'B7:B8', 'C7:C8',
            'D7:F7',
            'G7:G8', 'H7:H8', 'I7:I8', 'J7:J8', 'K7:K8',
            'L7:M7',
            'N7:N8',
        ];
        foreach ($merges78 as $range) {
            $sheet->mergeCells($range);
        }

        $sheet->getStyle('A7:N8')->applyFromArray([
            'font'      => ['name' => 'Arial', 'size' => 10, 'bold' => true, 'color' => ['argb' => 'FF000000']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::FILL_HEADER_BLUE]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(24.0);
        $sheet->getRowDimension(8)->setRowHeight(38.25);

        // Baris 9: Nomor Kolom 1 s.d. 14
        for ($c = 1; $c <= 14; $c++) {
            $colStr = Coordinate::stringFromColumnIndex($c);
            $sheet->setCellValue("{$colStr}9", $c);
        }
        $sheet->getStyle('A9:N9')->applyFromArray([
            'font'      => ['name' => 'Arial', 'size' => 10, 'bold' => true, 'color' => ['argb' => 'FF000000']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::FILL_NUMBER_GRAY]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'top'      => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'left'     => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'right'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'vertical' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'bottom'   => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getRowDimension(9)->setRowHeight(13.5);
        $sheet->getRowDimension(10)->setRowHeight(8.0);

        $dataStartRow = 11;
        $currentRow   = 11;

        if (empty($rows)) {
            $emptyRowCount = 11;
            for ($i = 0; $i < $emptyRowCount; $i++) {
                $this->stylePenguranganDataRow($sheet, $currentRow, [], $colWidths);
                $currentRow++;
            }
            $this->attachNihilWatermark($sheet, 'F14', 9, 16, 360, 82, 'Watermark NIHIL Pengurangan');
        } else {
            foreach ($rows as $idx => $r) {
                $sheet->setCellValue("A{$currentRow}", $idx + 1);
                $sheet->setCellValueExplicit("B{$currentRow}", $r['kode_barang'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("C{$currentRow}", $r['register'], DataType::TYPE_STRING);
                $sheet->setCellValue("D{$currentRow}", $r['nama_barang']);
                $sheet->setCellValue("E{$currentRow}", $r['merk_type']);
                $sheet->setCellValueExplicit("F{$currentRow}", $r['nomor_identitas'], DataType::TYPE_STRING);
                $sheet->setCellValue("G{$currentRow}", $r['bahan']);
                $sheet->setCellValue("H{$currentRow}", $r['asal']);
                $sheet->setCellValue("I{$currentRow}", $r['tahun']);
                $sheet->setCellValue("J{$currentRow}", $r['satuan']);
                $sheet->setCellValue("K{$currentRow}", $r['kondisi']);
                $sheet->setCellValue("L{$currentRow}", $r['jumlah']);
                $sheet->setCellValue("M{$currentRow}", $r['nilai']);
                $sheet->setCellValue("N{$currentRow}", $r['keterangan']);

                $this->stylePenguranganDataRow($sheet, $currentRow, [
                    'C' => $r['register'],
                    'D' => $r['nama_barang'],
                    'E' => $r['merk_type'],
                    'F' => $r['nomor_identitas'],
                    'N' => $r['keterangan'],
                ], $colWidths);
                $currentRow++;
            }
        }

        $dataEndRow = $currentRow - 1;
        $totalRow   = $currentRow;

        // Baris JUMLAH
        $sheet->setCellValue("A{$totalRow}", 'JUMLAH');
        $sheet->mergeCells("A{$totalRow}:K{$totalRow}");
        $sheet->setCellValue("L{$totalRow}", "=SUM(L{$dataStartRow}:L{$dataEndRow})");
        $sheet->setCellValue("M{$totalRow}", "=SUM(M{$dataStartRow}:M{$dataEndRow})");

        $sheet->getStyle("A{$totalRow}:N{$totalRow}")->applyFromArray([
            'font'      => ['name' => 'Arial', 'size' => 10, 'bold' => true, 'color' => ['argb' => 'FF000000']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::FILL_HEADER_BLUE]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$totalRow}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'numberFormat' => ['formatCode' => self::NUMFMT_INT],
        ]);
        $sheet->getStyle("M{$totalRow}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => self::NUMFMT_CURRENCY_COMMA],
        ]);
        $sheet->getRowDimension($totalRow)->setRowHeight(18.0);

        // Blok Tanda Tangan 2 Kolom Sejajar (Kiri B..E, Kanan L..N)
        $sigStart = $totalRow + 2;
        $this->renderPenguranganSignatures($sheet, $sigStart, $signDate, $signatories);
        $lastRow = $sigStart + 7;

        // PageSetup Landscape & PrintArea ketat A..N (bersih tanpa kolom liar O+)
        $ps = $sheet->getPageSetup();
        $ps->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $ps->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
        $ps->setFitToPage(true);
        $ps->setFitToWidth(1);
        $ps->setFitToHeight(0);
        $ps->setRowsToRepeatAtTopByStartAndEnd(7, 9);
        $ps->setPrintArea("A1:N{$lastRow}");

        $pm = $sheet->getPageMargins();
        $pm->setTop(0.39)->setRight(0.39)->setBottom(0.39)->setLeft(0.59)->setHeader(0.51)->setFooter(0.51);
    }

    private function stylePenguranganDataRow(Worksheet $sheet, int $row, array $wrappedTexts, array $colWidths): void
    {
        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
            'font'      => ['name' => 'Arial', 'size' => 10, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => [
                'top'      => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['argb' => 'FF000000']],
                'bottom'   => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['argb' => 'FF000000']],
                'left'     => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'right'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                'vertical' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$row}:C{$row}")->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle("I{$row}:K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$row}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'numberFormat' => ['formatCode' => self::NUMFMT_INT],
        ]);
        $sheet->getStyle("M{$row}")->applyFromArray([
            'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'numberFormat' => ['formatCode' => self::NUMFMT_CURRENCY_COMMA],
        ]);

        $height = $this->calculateDynamicRowHeight($wrappedTexts, $colWidths, 24.0, 14.0);
        $sheet->getRowDimension($row)->setRowHeight($height);
    }

    private function renderPenguranganSignatures(Worksheet $sheet, int $startRow, string $signDate, array $signatories): void
    {
        $r0 = $startRow;
        $r1 = $startRow + 1;
        $r2 = $startRow + 2;
        $r3 = $startRow + 3;
        $r5 = $startRow + 5;
        $r6 = $startRow + 6;
        $r7 = $startRow + 7;

        $sheet->setCellValue("B{$r0}", 'MENGETAHUI,');
        $sheet->setCellValue("B{$r1}", 'DIREKTUR RSUD dr. H. KOESNADI BONDOWOSO');
        $sheet->setCellValue("B{$r5}", $signatories['direktur_nama']);
        $sheet->setCellValue("B{$r6}", $signatories['direktur_pangkat']);
        $sheet->setCellValue("B{$r7}", 'NIP. ' . $signatories['direktur_nip']);

        $sheet->mergeCells("B{$r0}:E{$r0}");
        $sheet->mergeCells("B{$r1}:E{$r3}");
        $sheet->mergeCells("B{$r5}:E{$r5}");
        $sheet->mergeCells("B{$r6}:E{$r6}");
        $sheet->mergeCells("B{$r7}:E{$r7}");

        $sheet->setCellValue("L{$r0}", "Bondowoso, {$signDate}");
        $sheet->setCellValue("L{$r2}", 'PENGURUS BARANG ASET');
        $sheet->setCellValue("L{$r5}", $signatories['pengurus_nama']);
        $sheet->setCellValue("L{$r6}", 'NIP. ' . $signatories['pengurus_nip']);

        $sheet->mergeCells("L{$r0}:N{$r0}");
        $sheet->mergeCells("L{$r2}:N{$r2}");
        $sheet->mergeCells("L{$r5}:N{$r5}");
        $sheet->mergeCells("L{$r6}:N{$r6}");

        $sheet->getStyle("B{$r0}:N{$r7}")->applyFromArray([
            'font'      => ['name' => 'Calibri', 'size' => 12, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getStyle("B{$r5}:N{$r5}")->getFont()->setBold(true);

        $sheet->getRowDimension($r0)->setRowHeight(15.75);
        $sheet->getRowDimension($r1)->setRowHeight(15.75);
        $sheet->getRowDimension($r2)->setRowHeight(28.5);
        $sheet->getRowDimension($r3)->setRowHeight(15.75);
        $sheet->getRowDimension($startRow + 4)->setRowHeight(15.75);
        $sheet->getRowDimension($r5)->setRowHeight(15.75);
        $sheet->getRowDimension($r6)->setRowHeight(15.75);
        $sheet->getRowDimension($r7)->setRowHeight(15.75);
    }

    private function resolvePenambahanGroupTitle(AstapReklas $reklas, Astap $astap): string
    {
        $asal = strtoupper(trim((string) ($reklas->asal_kib ?: '')));
        if ($asal === 'PERSEDIAAN' || $astap->sumber_dana === 'belanja_barang') {
            return 'Reklas dari Persediaan Ke Extracom';
        }
        return 'Reklas dari Aset Tetap Ke Extracom ';
    }

    private function resolveAsalPerolehan(Astap $astap, array $spec): string
    {
        if (!empty($spec['asal_usul']) && trim((string) $spec['asal_usul']) !== '-') {
            return (string) $spec['asal_usul'];
        }
        return match ($astap->sumber_dana) {
            'hibah' => 'Hibah',
            'mutasi_eksternal', 'pelimpahan_skpd', 'mutasi_masuk', 'mutasi', 'pelimpahan' => 'Mutasi Masuk OPD',
            'kemitraan' => 'Kemitraan',
            'belanja_barang', 'belanja_rekening' => 'Belanja Barang',
            default => 'Pembelian',
        };
    }

    private function buildPenambahanKeterangan(?AstapReklas $reklas, Astap $astap, ?string $kodeRek, ?string $namaBelanja): string
    {
        if ($reklas) {
            $asalKode = trim((string) ($reklas->asal_kode ?: $kodeRek ?: ''));
            $asalNama = trim((string) ($reklas->asal_nama ?: $namaBelanja ?: 'Aset Tetap'));
            $desc     = trim("Reklas dari  {$asalKode} {$asalNama} Ke Extracomptable");
            $extra    = array_filter([
                $reklas->nomor_ba_reklas ? "No. BA: {$reklas->nomor_ba_reklas}" : null,
                $reklas->alasan_reklas ?: $reklas->keterangan,
            ]);
            if (!empty($extra)) {
                $desc .= ' (' . implode(' — ', $extra) . ')';
            }
            return $desc;
        }
        return $astap->keterangan_tambahan ?: 'Reklas dari Aset Tetap Ke Extracomptable';
    }

    private function resolveSignatureDate(string $yearLabel, ?int $twInt): string
    {
        return match ($twInt) {
            1 => "31 Maret {$yearLabel}",
            2 => "30 Juni {$yearLabel}",
            3 => "30 September {$yearLabel}",
            default => "31 Desember {$yearLabel}",
        };
    }

    private function resolveSignatories(string $yearLabel, ?int $twInt): array
    {
        $bast = AstapBastTriwulan::where('tahun', (int) $yearLabel)
            ->when($twInt, fn ($q) => $q->where('triwulan', 'TW' . $twInt))
            ->orderByDesc('id')
            ->first();

        return [
            'direktur_nama'    => 'Dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR',
            'direktur_pangkat' => 'Pembina Tk.I-IV/b',
            'direktur_nip'     => '19771002 200604 1 006',
            'pengurus_nama'    => $bast?->pihak2_nama ?: 'BUDI HARTONO,S.Sos',
            'pengurus_nip'     => $bast?->pihak2_nip ?: '19760229 200801 1 010',
        ];
    }

    private function matchesDeletedPeriod($deletedAt, $tahunPerolehan, ?string $astapTw, string $filterTahun, ?int $filterTwInt): bool
    {
        $delDate = $deletedAt ? Carbon::parse($deletedAt) : null;
        $itemYear = $delDate ? (string) $delDate->year : (string) $tahunPerolehan;
        if ($filterTahun !== 'all' && is_numeric($filterTahun) && $itemYear !== (string) (int) $filterTahun) {
            return false;
        }
        if ($filterTwInt !== null) {
            $itemTwInt = $delDate
                ? (intdiv($delDate->month - 1, 3) + 1)
                : ($this->parseTriwulanInt($astapTw) ?? 1);
            if ($itemTwInt !== $filterTwInt) {
                return false;
            }
        }
        return true;
    }

    private function twVariantStrings(int $twInt): array
    {
        return match ($twInt) {
            1 => ['1', 'TW1', 'TW 1', 'TW I', 'TWI', 'Triwulan I', 'Triwulan 1', 'Q1', 'I'],
            2 => ['2', 'TW2', 'TW 2', 'TW II', 'TWII', 'Triwulan II', 'Triwulan 2', 'Q2', 'II'],
            3 => ['3', 'TW3', 'TW 3', 'TW III', 'TWIII', 'Triwulan III', 'Triwulan 3', 'Q3', 'III'],
            4 => ['4', 'TW4', 'TW 4', 'TW IV', 'TWIV', 'Triwulan IV', 'Triwulan 4', 'Q4', 'IV'],
            default => [],
        };
    }

    private function decodeSpec($spec): array
    {
        if (is_array($spec)) {
            return $spec;
        }
        if (is_string($spec)) {
            $decoded = json_decode($spec, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    private function pickRepeaterItemForIndex(array $spec, int $index): array
    {
        if (!empty($spec['mesin_items'][$index]) && is_array($spec['mesin_items'][$index])) {
            return $spec['mesin_items'][$index];
        }
        if (!empty($spec['lainnya_items'][$index]) && is_array($spec['lainnya_items'][$index])) {
            return $spec['lainnya_items'][$index];
        }
        return [];
    }

    private function extractSpecAttributes(array $spec): array
    {
        $firstMesin   = !empty($spec['mesin_items'][0]) && is_array($spec['mesin_items'][0]) ? $spec['mesin_items'][0] : [];
        $firstLainnya = !empty($spec['lainnya_items'][0]) && is_array($spec['lainnya_items'][0]) ? $spec['lainnya_items'][0] : [];
        $firstTanah   = !empty($spec['tanah_items'][0]) && is_array($spec['tanah_items'][0]) ? $spec['tanah_items'][0] : [];

        return [
            'merk' => $this->firstNonEmpty(
                $spec['merk'] ?? null,
                $firstMesin['mesin_merk'] ?? null,
                $spec['buku_judul'] ?? null,
                $firstLainnya['lainnya_buku_pencipta'] ?? null
            ) ?? '-',
            'type' => $this->firstNonEmpty(
                $spec['type'] ?? null,
                $spec['tipe'] ?? null,
                $spec['model'] ?? null,
                $firstMesin['mesin_type'] ?? null
            ) ?? '-',
            'bahan' => $this->firstNonEmpty(
                $spec['bahan'] ?? null,
                $firstMesin['mesin_bahan'] ?? null
            ) ?? '-',
            'sertifikat_nomor' => $this->firstNonEmpty(
                $spec['sertifikat_no'] ?? null,
                $spec['sertifikat_nomor'] ?? null,
                $spec['tanah_sertifikat_no'] ?? null,
                $firstTanah['tanah_sertifikat_no'] ?? null
            ) ?? '-',
            'no_pabrik' => $this->firstNonEmpty(
                $spec['no_pabrik'] ?? null,
                $spec['mesin_nomor_pabrik'] ?? null,
                $spec['mesin_no_pabrik'] ?? null,
                $firstMesin['mesin_no_pabrik'] ?? null
            ) ?? '-',
            'no_rangka' => $this->firstNonEmpty(
                $spec['no_rangka'] ?? null,
                $spec['mesin_nomor_rangka'] ?? null,
                $spec['rangka'] ?? null,
                $firstMesin['mesin_no_rangka'] ?? null
            ) ?? '-',
            'no_mesin' => $this->firstNonEmpty(
                $spec['no_mesin'] ?? null,
                $spec['mesin_nomor_mesin'] ?? null,
                $spec['mesin'] ?? null,
                $firstMesin['mesin_no_mesin'] ?? null
            ) ?? '-',
        ];
    }

    private function firstNonEmpty(...$candidates): ?string
    {
        foreach ($candidates as $val) {
            if ($val === null) {
                continue;
            }
            $s = trim((string) $val);
            if ($s !== '' && $s !== '-') {
                return $s;
            }
        }
        return null;
    }

    private function calculateDynamicRowHeight(array $textsByCol, array $colWidths, float $minHeight, float $lineHeight): float
    {
        $maxLines = 1;
        foreach ($textsByCol as $col => $text) {
            $str = trim((string) $text);
            if ($str === '') {
                continue;
            }
            $width    = max(6.0, (float) ($colWidths[$col] ?? 12.0) - 1.5);
            $segments = explode("\n", $str);
            $lines    = 0;
            foreach ($segments as $seg) {
                $len   = mb_strlen(trim($seg));
                $lines += max(1, (int) ceil($len / $width));
            }
            if ($lines > $maxLines) {
                $maxLines = $lines;
            }
        }

        return max($minHeight, ($maxLines * $lineHeight) + 6.0);
    }

    /**
     * Sematkan watermark transparan bertuliskan "NIHIL" di area baris kosong tabel
     * secara 100% programatik menggunakan PHP GD & MemoryDrawing (tanpa file gambar eksternal).
     */
    private function attachNihilWatermark(
        Worksheet $sheet,
        string $coordinate,
        int $offsetX,
        int $offsetY,
        int $displayWidth,
        int $displayHeight,
        string $name
    ): void {
        $imageResource = $this->createNihilImageResource($displayWidth * 2, $displayHeight * 2);
        if ($imageResource === null) {
            return;
        }

        $drawing = new MemoryDrawing();
        $drawing->setName($name);
        $drawing->setDescription('Watermark NIHIL - Data Kosong');
        $drawing->setImageResource($imageResource);
        $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
        $drawing->setMimeType(MemoryDrawing::MIMETYPE_PNG);
        $drawing->setCoordinates($coordinate);
        $drawing->setOffsetX($offsetX);
        $drawing->setOffsetY($offsetY);
        $drawing->setWidthAndHeight($displayWidth, $displayHeight);
        $drawing->setWorksheet($sheet);
    }

    /**
     * Buat image resource GD berlatar transparan bertuliskan "NIHIL" (Times New Roman / Serif,
     * warna #333333 + bayangan #B2B2B2 lembut) sesuai format WordArt pada lampiran asli.
     *
     * @return \GdImage|resource|null
     */
    private function createNihilImageResource(int $targetWidth, int $targetHeight)
    {
        if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
            return null;
        }

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($target === false) {
            return null;
        }
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparentTarget = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefill($target, 0, 0, $transparentTarget);

        $fontPath = $this->resolveSerifFontPath();
        $text = 'NIHIL';

        if ($fontPath !== null && function_exists('imagettfbbox') && function_exists('imagettftext')) {
            $fontSize = 110;
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
            if ($bbox !== false) {
                $minX = min($bbox[0], $bbox[2], $bbox[4], $bbox[6]);
                $maxX = max($bbox[0], $bbox[2], $bbox[4], $bbox[6]);
                $minY = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
                $maxY = max($bbox[1], $bbox[3], $bbox[5], $bbox[7]);

                $shadowDx = 7;
                $shadowDy = 5;
                $pad = 8;

                $srcWidth  = (int) ceil($maxX - $minX) + $shadowDx + ($pad * 2);
                $srcHeight = (int) ceil($maxY - $minY) + $shadowDy + ($pad * 2);

                $src = imagecreatetruecolor(max(1, $srcWidth), max(1, $srcHeight));
                if ($src !== false) {
                    imagealphablending($src, false);
                    imagesavealpha($src, true);
                    $transparentSrc = imagecolorallocatealpha($src, 0, 0, 0, 127);
                    imagefill($src, 0, 0, $transparentSrc);
                    imagealphablending($src, true);

                    $shadowColor = imagecolorallocatealpha($src, 0xB2, 0xB2, 0xB2, 26);
                    $mainColor   = imagecolorallocatealpha($src, 0x33, 0x33, 0x33, 0);

                    $originX = $pad - $minX;
                    $originY = $pad - $minY;

                    imagettftext($src, $fontSize, 0, (int) round($originX + $shadowDx), (int) round($originY + $shadowDy), $shadowColor, $fontPath, $text);
                    imagettftext($src, $fontSize, 0, (int) round($originX), (int) round($originY), $mainColor, $fontPath, $text);

                    imagecopyresampled($target, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $srcWidth, $srcHeight);
                    imagedestroy($src);

                    return $target;
                }
            }
        }

        // Fallback lintas-platform jika file font TTF serif tidak ditemukan
        $fbWidth = 64;
        $fbHeight = 20;
        $fb = imagecreatetruecolor($fbWidth, $fbHeight);
        if ($fb !== false) {
            imagealphablending($fb, false);
            imagesavealpha($fb, true);
            $transparentFb = imagecolorallocatealpha($fb, 0, 0, 0, 127);
            imagefill($fb, 0, 0, $transparentFb);
            imagealphablending($fb, true);

            $shadowColor = imagecolorallocatealpha($fb, 0xB2, 0xB2, 0xB2, 26);
            $mainColor   = imagecolorallocatealpha($fb, 0x33, 0x33, 0x33, 0);
            imagestring($fb, 5, 11, 3, $text, $shadowColor);
            imagestring($fb, 5, 10, 2, $text, $mainColor);

            imagecopyresampled($target, $fb, 0, 0, 0, 0, $targetWidth, $targetHeight, $fbWidth, $fbHeight);
            imagedestroy($fb);
        }

        return $target;
    }

    private function resolveSerifFontPath(): ?string
    {
        $candidates = [
            'C:\\Windows\\Fonts\\times.ttf',
            'C:\\Windows\\Fonts\\timesbd.ttf',
            'C:\\Windows\\Fonts\\georgia.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSerif-Regular.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSerif.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSerif.ttf',
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}

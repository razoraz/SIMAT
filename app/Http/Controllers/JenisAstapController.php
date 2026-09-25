<?php

namespace App\Http\Controllers;

use App\Models\JenisAstap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisAstapController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisAstap::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('uraian_sub_sub_rincian', 'like', "%{$search}%")
                  ->orWhere('sub_sub_rincian_objek', 'like', "%{$search}%")
                  ->orWhere('uraian_sub_rincian', 'like', "%{$search}%")
                  ->orWhere('sub_rincian_objek', 'like', "%{$search}%")
                  ->orWhere('nama_jenis', 'like', "%{$search}%")
                  ->orWhere('jenis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis') && $request->jenis !== 'all') {
            $jenisVal = $request->jenis;
            if ($jenisVal === '1.3.6') {
                $query->where(function ($q) {
                    $q->where('jenis', 'like', '1.3.6%')
                      ->orWhere('nama_jenis', 'like', '%KONSTRUKSI%');
                });
            } elseif ($jenisVal === '1.3.7') {
                $query->where(function ($q) {
                    $q->where('jenis', 'like', '1.3.7%')
                      ->orWhere('jenis', 'like', '1.3.5.07%')
                      ->orWhere('nama_jenis', 'like', '%RENOVASI%');
                });
            } else {
                $query->where('jenis', 'like', $jenisVal . '%');
            }
        }

        $kode108List = $query->paginate(10)->withQueryString();
        $uniqueJenis = JenisAstap::select('jenis', 'nama_jenis')->distinct()->get();
        $uniqueSubRincian = JenisAstap::select('jenis', 'sub_rincian_objek', 'uraian_sub_rincian')
            ->whereNotNull('sub_rincian_objek')
            ->where('sub_rincian_objek', '!=', '')
            ->whereNotNull('uraian_sub_rincian')
            ->where('uraian_sub_rincian', '!=', '')
            ->distinct()
            ->get();
        $totalCount = JenisAstap::count();

        return view('pages.master.jenis_astap.index', compact('kode108List', 'uniqueJenis', 'uniqueSubRincian', 'totalCount'));
    }

    public function searchSubSub(Request $request)
    {
        $q = $request->query('q', '');
        $subRincian = $request->query('sub_rincian', '');
        $jenis = $request->query('jenis', '');

        $query = JenisAstap::query()
            ->select('sub_sub_rincian_objek', 'uraian_sub_sub_rincian')
            ->whereNotNull('sub_sub_rincian_objek')
            ->where('sub_sub_rincian_objek', '!=', '')
            ->whereNotNull('uraian_sub_sub_rincian')
            ->where('uraian_sub_sub_rincian', '!=', '');

        if (!empty($subRincian)) {
            $query->where('sub_rincian_objek', $subRincian);
        } elseif (!empty($jenis)) {
            $query->where('jenis', $jenis);
        }

        if (!empty($q)) {
            $query->where(function ($sq) use ($q) {
                $sq->where('uraian_sub_sub_rincian', 'like', "%{$q}%")
                   ->orWhere('sub_sub_rincian_objek', 'like', "%{$q}%");
            });
        }

        $totalCount = (clone $query)->distinct()->count('sub_sub_rincian_objek');
        $results = $query->select('sub_sub_rincian_objek', 'uraian_sub_sub_rincian')
            ->distinct()
            ->limit(5)
            ->get();

        return response()->json([
            'total' => $totalCount,
            'items' => $results
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string',
            'nama_jenis' => 'required|string',
            'sub_rincian_objek' => 'required|string',
            'uraian_sub_rincian' => 'required|string',
            'sub_sub_rincian_objek' => 'required|string',
            'uraian_sub_sub_rincian' => 'required|string',
        ]);

        JenisAstap::create($validated);

        return redirect()->back()->with('success', 'Data Jenis ASTAP berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jenisAstap = JenisAstap::findOrFail($id);

        $validated = $request->validate([
            'jenis' => 'required|string',
            'nama_jenis' => 'required|string',
            'sub_rincian_objek' => 'required|string',
            'uraian_sub_rincian' => 'required|string',
            'sub_sub_rincian_objek' => 'required|string',
            'uraian_sub_sub_rincian' => 'required|string',
        ]);

        $jenisAstap->update($validated);

        return redirect()->back()->with('success', 'Data Jenis ASTAP berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jenisAstap = JenisAstap::findOrFail($id);
        $jenisAstap->delete();

        return redirect()->back()->with('success', 'Data Jenis ASTAP berhasil dihapus.');
    }

    public function import(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:20480',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['csv', 'txt', 'xlsx', 'xls'])) {
                        $fail('File harus berformat CSV (.csv) atau Excel.');
                    }
                }
            ],
        ]);

        $resetExisting = $request->boolean('reset_existing');
        if ($resetExisting) {
            JenisAstap::truncate();
            $existingKeys = [];
        } else {
            $existingKeys = JenisAstap::select('jenis', 'sub_sub_rincian_objek', 'uraian_sub_sub_rincian')
                ->get()
                ->mapWithKeys(function ($item) {
                    $key = trim($item->jenis) . '|' . trim($item->sub_sub_rincian_objek) . '|' . strtolower(trim($item->uraian_sub_sub_rincian));
                    return [$key => true];
                })
                ->toArray();
        }

        $defaultNamaJenis = [
            '1.3.1' => 'TANAH',
            '1.3.2' => 'PERALATAN DAN MESIN',
            '1.3.3' => 'GEDUNG DAN BANGUNAN',
            '1.3.4' => 'JALAN, IRIGASI DAN JARINGAN',
            '1.3.5' => 'ASET TETAP LAINNYA',
            '1.3.6' => 'KONSTRUKSI DALAM PENGERJAAN',
            '1.5.3' => 'ASET TIDAK BERWUJUD',
            '1.3.7' => 'ASET TETAP DALAM RENOVASI'
        ];

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();

        // 1. Baca baris data (mendukung .xlsx maupun .csv)
        if ($ext === 'xlsx') {
            $rows = $this->parseXlsx($filePath);
            if (empty($rows)) {
                $rows = $this->parseCsv($filePath);
            }
        } else {
            $rows = $this->parseCsv($filePath);
        }

        if (empty($rows)) {
            return redirect()->back()->with('error', 'File Excel/CSV kosong atau tidak dapat dibaca.');
        }

        $importedCount = 0;
        $skippedCount = 0;
        $insertData = [];
        $now = now();

        foreach ($rows as $row) {
            if (empty($row) || count(array_filter($row)) === 0) continue;

            $col0 = trim((string)($row[0] ?? ''));
            $col1 = trim((string)($row[1] ?? ''));

            // Lewati baris header (NOMOR, NO, JENIS, KODE)
            if (in_array(strtolower($col0), ['nomor', 'no', 'jenis', 'kode', 'kode jenis']) || strtolower($col1) === 'jenis' || strtolower($col0) === 'jenis') {
                continue;
            }

            // Deteksi offset: jika kolom 0 nomor urut (angka), offset = 1; jika langsung kode (1.3.1), offset = 0
            $offset = 1;
            if (preg_match('/^\d+(\.\d+)+$/', $col0)) {
                $offset = 0;
            }

            $jenis = trim((string)($row[0 + $offset] ?? ''));
            $namaJenis = trim((string)($row[1 + $offset] ?? ''));
            $subRincian = trim((string)($row[2 + $offset] ?? ''));
            $uraianSubRincian = trim((string)($row[3 + $offset] ?? ''));
            $subSubRincian = trim((string)($row[4 + $offset] ?? ''));
            $uraianSubSubRincian = trim((string)($row[5 + $offset] ?? ''));

            if (empty($namaJenis) && isset($defaultNamaJenis[$jenis])) {
                $namaJenis = $defaultNamaJenis[$jenis];
            }

            if (empty($jenis) || empty($subSubRincian) || !preg_match('/^\d+(\.\d+)+$/', $jenis)) {
                continue;
            }

            $compositeKey = $jenis . '|' . $subSubRincian . '|' . strtolower($uraianSubSubRincian);

            if (!$resetExisting && isset($existingKeys[$compositeKey])) {
                $skippedCount++;
                continue;
            }

            $existingKeys[$compositeKey] = true;

            $insertData[] = [
                'jenis' => $jenis,
                'nama_jenis' => $namaJenis,
                'sub_rincian_objek' => $subRincian,
                'uraian_sub_rincian' => $uraianSubRincian,
                'sub_sub_rincian_objek' => $subSubRincian,
                'uraian_sub_sub_rincian' => $uraianSubSubRincian,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $importedCount++;
        }

        if (!empty($insertData)) {
            DB::transaction(function () use ($insertData) {
                foreach (array_chunk($insertData, 500) as $chunk) {
                    JenisAstap::insert($chunk);
                }
            });
        }

        $msg = "Berhasil mengimpor {$importedCount} data Jenis ASTAP!";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} data duplikat dilewati).";
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Membaca file Microsoft Excel modern (.xlsx) secara native.
     */
    private function parseXlsx(string $filePath): array
    {
        $zipEntries = $this->unzipXlsx($filePath);
        if (empty($zipEntries)) {
            return [];
        }

        // 1. Ambil Shared Strings (kumpulan teks pada Excel)
        $sharedStrings = [];
        if (isset($zipEntries['xl/sharedStrings.xml'])) {
            try {
                $xml = simplexml_load_string($zipEntries['xl/sharedStrings.xml']);
                if ($xml && isset($xml->si)) {
                    foreach ($xml->si as $si) {
                        if (isset($si->t)) {
                            $sharedStrings[] = (string)$si->t;
                        } elseif (isset($si->r)) {
                            $txt = '';
                            foreach ($si->r as $r) {
                                $txt .= (string)$r->t;
                            }
                            $sharedStrings[] = $txt;
                        } else {
                            $sharedStrings[] = '';
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 2. Temukan sheet data (sheet1.xml)
        $sheetXml = $zipEntries['xl/worksheets/sheet1.xml'] ?? null;
        if (!$sheetXml) {
            foreach ($zipEntries as $name => $content) {
                if (preg_match('#xl/worksheets/sheet\d+\.xml#i', $name)) {
                    $sheetXml = $content;
                    break;
                }
            }
        }

        if (!$sheetXml) {
            return [];
        }

        $rows = [];
        try {
            $xml = simplexml_load_string($sheetXml);
            if ($xml && isset($xml->sheetData->row)) {
                foreach ($xml->sheetData->row as $rowNode) {
                    $rowCells = [];
                    foreach ($rowNode->c as $c) {
                        $cellRef = (string)$c['r']; // e.g. "A1", "B1", "G5"
                        preg_match('/^([A-Z]+)/', $cellRef, $m);
                        $colLetter = $m[1] ?? 'A';
                        $colIdx = $this->colLetterToNum($colLetter);

                        $t = (string)$c['t'];
                        $val = '';
                        if ($t === 's') {
                            $sIdx = (int)$c->v;
                            $val = $sharedStrings[$sIdx] ?? '';
                        } elseif ($t === 'inlineStr' && isset($c->is->t)) {
                            $val = (string)$c->is->t;
                        } elseif (isset($c->v)) {
                            $val = (string)$c->v;
                        }

                        $rowCells[$colIdx] = trim($val);
                    }

                    if (!empty($rowCells)) {
                        $maxIdx = max(array_keys($rowCells));
                        $row = [];
                        for ($i = 0; $i <= $maxIdx; $i++) {
                            $row[$i] = $rowCells[$i] ?? '';
                        }
                        $rows[] = $row;
                    }
                }
            }
        } catch (\Throwable $e) {}

        return $rows;
    }

    /**
     * Membaca file CSV dengan deteksi otomatis pemisah.
     */
    private function parseCsv(string $filePath): array
    {
        $handle = @fopen($filePath, 'r');
        if (!$handle) return [];

        $firstLine = fgets($handle);
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', (string)$firstLine);

        $delimiters = [
            ';' => substr_count($firstLine, ';'),
            ',' => substr_count($firstLine, ','),
            "\t" => substr_count($firstLine, "\t"),
            '|' => substr_count($firstLine, '|')
        ];
        arsort($delimiters);
        $delimiter = key($delimiters);
        if ($delimiters[$delimiter] === 0) {
            $delimiter = ';';
        }

        rewind($handle);
        $rows = [];
        $isFirst = true;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (empty($data) || count(array_filter($data)) === 0) continue;

            if ($isFirst) {
                $isFirst = false;
                if (isset($data[0])) {
                    $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$data[0]);
                }
            }

            $rows[] = array_map('trim', $data);
        }

        fclose($handle);
        return $rows;
    }

    private function colLetterToNum(string $letter): int
    {
        $num = 0;
        $len = strlen($letter);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($letter[$i]) - 64);
        }
        return $num - 1;
    }

    private function unzipXlsx(string $filePath): array
    {
        $entries = [];

        if (class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    $entries[$name] = $zip->getFromIndex($i);
                }
                $zip->close();
                return $entries;
            }
        }

        $content = @file_get_contents($filePath);
        if (!$content) return [];

        $len = strlen($content);
        $pos = 0;

        while ($pos < $len - 30) {
            $sig = substr($content, $pos, 4);
            if ($sig !== "PK\x03\x04") {
                $pos++;
                continue;
            }

            $compMethod = unpack('v', substr($content, $pos + 8, 2))[1] ?? 0;
            $compSize = unpack('V', substr($content, $pos + 18, 4))[1] ?? 0;
            $nameLen = unpack('v', substr($content, $pos + 26, 2))[1] ?? 0;
            $extraLen = unpack('v', substr($content, $pos + 28, 2))[1] ?? 0;

            $name = substr($content, $pos + 30, $nameLen);
            $dataOffset = $pos + 30 + $nameLen + $extraLen;
            $compressedData = substr($content, $dataOffset, $compSize);

            $uncompressed = false;
            if ($compMethod === 8 && function_exists('gzinflate')) {
                $uncompressed = @gzinflate($compressedData);
            } elseif ($compMethod === 0) {
                $uncompressed = $compressedData;
            }

            if ($uncompressed !== false) {
                $entries[$name] = $uncompressed;
            }

            $pos = $dataOffset + $compSize;
        }

        return $entries;
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=template_import_jenis_astap.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['NOMOR', 'JENIS', 'NAMA JENIS', 'SUB RINCIAN OBJEK', 'URAIAN SUB RINCIAN', 'SUB - SUB RINCIAN OBJEK', 'URAIAN SUB-SUB RINCIAN'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            fputcsv($file, ['1', '1.3.1', 'TANAH', '1.3.1.01.01.01', 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL', '1.3.1.01.01.01.001', 'Tanah Bangunan Rumah Negara Golongan I'], ';');
            fputcsv($file, ['2', '1.3.1', 'TANAH', '1.3.1.01.01.01', 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL', '1.3.1.01.01.01.002', 'Tanah Bangunan Rumah Negara Golongan II'], ';');
            fputcsv($file, ['3', '1.3.2', 'PERALATAN DAN MESIN', '1.3.2.02.01.01', 'ALAT KEDOKTERAN UMUM & DIAGNOSTIK', '1.3.2.02.01.01.002', 'USG 4D Color Doppler Imaging Unit'], ';');
            fputcsv($file, ['4', '1.3.3', 'GEDUNG DAN BANGUNAN', '1.3.3.01.01.01', 'BANGUNAN GEDUNG TEMPAT KERJA RSUD', '1.3.3.01.01.01.002', 'Gedung Instalasi Gawat Darurat (IGD) Terpadu'], ';');
            fputcsv($file, ['5', '1.5.3', 'ASET TIDAK BERWUJUD', '1.5.3.01.01.01', 'SOFTWARE SISTEM INFORMASI KESEHATAN (SIMRS)', '1.5.3.01.01.01.001', 'Software SIMAT-RK RSUD Dr. H. Koesnandi'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
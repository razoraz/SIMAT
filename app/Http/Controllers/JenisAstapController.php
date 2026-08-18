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

        $kode108List = $query->paginate(50)->withQueryString();
        $uniqueJenis = JenisAstap::select('jenis', 'nama_jenis')->distinct()->get();
        $totalCount = JenisAstap::count();

        return view('pages.master_jenis_astap', compact('kode108List', 'uniqueJenis', 'totalCount'));
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
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480',
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

        $file = $request->file('file');
        $importedCount = 0;
        $skippedCount = 0;
        $insertData = [];
        $now = now();

        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                foreach ($rows as $row) {
                    if (empty($row) || count(array_filter($row)) === 0) continue;

                    $col0 = trim((string)($row[0] ?? ''));
                    $col1 = trim((string)($row[1] ?? ''));

                    // Skip header title row (e.g. NOMOR, JENIS, NAMA JENIS)
                    if (strtolower($col0) === 'nomor' || strtolower($col0) === 'no' || strtolower($col1) === 'jenis') {
                        continue;
                    }

                    // Smart offset detection:
                    // If Column 0 has a dot notation like '1.3.1', offset is 0.
                    // Otherwise (Column 0 is NOMOR or blank), JENIS is in Column 1 (offset = 1).
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

                    // Strict check: Ignore blank/invalid rows (must have a valid jenis code and sub-sub rincian code)
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
                    $msg .= " ({$skippedCount} data yang sudah ada dilewati untuk mencegah duplikasi).";
                }

                return redirect()->back()->with('success', $msg);
            } catch (\Exception $e) {
                // Fallback to CSV parser if exception occurs
            }
        }

        $path = $file->getRealPath();
        $content = file_get_contents($path);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $delimiter = ',';
        if (substr_count($content, ';') > substr_count($content, ',')) {
            $delimiter = ';';
        }

        $lines = explode("\n", str_replace("\r\n", "\n", $content));
        $insertData = [];
        $importedCount = 0;
        $skippedCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $row = str_getcsv($line, $delimiter);

            $col0 = trim((string)($row[0] ?? ''));
            $col1 = trim((string)($row[1] ?? ''));

            if (strtolower($col0) === 'nomor' || strtolower($col0) === 'no' || strtolower($col1) === 'jenis') {
                continue;
            }

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
            $msg .= " ({$skippedCount} data yang sudah ada dilewati untuk mencegah duplikasi).";
        }

        return redirect()->back()->with('success', $msg);
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

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

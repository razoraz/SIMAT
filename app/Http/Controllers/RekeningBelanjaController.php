<?php

namespace App\Http\Controllers;

use App\Models\RekeningBelanja;
use Illuminate\Http\Request;

class RekeningBelanjaController extends Controller
{
    private $kelompokLookup = [
        '5.2.01' => 'Belanja Modal Tanah',
        '5.2.02' => 'Belanja Modal Peralatan dan Mesin',
        '5.2.03' => 'Belanja Modal Gedung dan Bangunan',
        '5.2.04' => 'Belanja Modal Jalan, Jaringan dan Irigasi',
        '5.2.05' => 'Belanja Modal Aset Tetap Lainnya',
        '5.2.06' => 'Belanja Modal Aset Tidak Berwujud',
    ];

    public function index(Request $request)
    {
        // Seed default dummy data if table is empty
        if (RekeningBelanja::count() === 0) {
            $defaultData = [
                [
                    'kelompok' => '5.2.02',
                    'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                    'kode_rek' => '5.2.02.05.02.0006',
                    'nama_belanja' => 'Belanja Modal Alat Rumah Tangga Lainnya (Home Use)',
                ],
                [
                    'kelompok' => '5.2.02',
                    'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                    'kode_rek' => '5.2.02.08.01.0005',
                    'nama_belanja' => 'Belanja Modal Alat Kedokteran Radiologi & Imaging',
                ],
                [
                    'kelompok' => '5.2.02',
                    'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin',
                    'kode_rek' => '5.2.02.08.01.0012',
                    'nama_belanja' => 'Belanja Modal Alat Kedokteran ICU & Ruang Rawat Intensif',
                ],
                [
                    'kelompok' => '5.2.03',
                    'nama_kelompok' => 'Belanja Modal Gedung dan Bangunan',
                    'kode_rek' => '5.2.03.01.01.0001',
                    'nama_belanja' => 'Belanja Modal Bangunan Gedung Rawat Inap & Poliklinik',
                ],
                [
                    'kelompok' => '5.2.04',
                    'nama_kelompok' => 'Belanja Modal Jalan, Jaringan dan Irigasi',
                    'kode_rek' => '5.2.04.03.01.0004',
                    'nama_belanja' => 'Belanja Modal Instalasi Jaringan Pipa Gas Oksigen Sentral Medis',
                ],
                [
                    'kelompok' => '5.2.01',
                    'nama_kelompok' => 'Belanja Modal Tanah',
                    'kode_rek' => '5.2.01.01.01.0002',
                    'nama_belanja' => 'Belanja Modal Pengadaan Tanah Fasilitas Pelayanan Kesehatan',
                ],
                [
                    'kelompok' => '5.2.05',
                    'nama_kelompok' => 'Belanja Modal Aset Tetap Lainnya',
                    'kode_rek' => '5.2.05.01.01.0003',
                    'nama_belanja' => 'Belanja Modal Bahan Pustaka dan Jurnal Ilmiah Kedokteran',
                ],
                [
                    'kelompok' => '5.2.06',
                    'nama_kelompok' => 'Belanja Modal Aset Tidak Berwujud',
                    'kode_rek' => '5.2.06.01.01.0001',
                    'nama_belanja' => 'Belanja Modal Software Sistem Informasi Manajemen RS (SIMRS)',
                ],
            ];

            foreach ($defaultData as $item) {
                RekeningBelanja::create($item);
            }
        }

        $query = RekeningBelanja::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_rek', 'like', "%{$search}%")
                  ->orWhere('nama_belanja', 'like', "%{$search}%")
                  ->orWhere('kelompok', 'like', "%{$search}%")
                  ->orWhere('nama_kelompok', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelompok') && $request->kelompok !== 'all') {
            $query->where('kelompok', $request->kelompok);
        }

        $rekeningList = $query->orderBy('kelompok')->orderBy('kode_rek')->get();
        $totalCount = RekeningBelanja::count();

        return view('pages.master_rekening_belanja', compact('rekeningList', 'totalCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelompok' => 'required|string|max:50',
            'kode_rek' => 'required|string|max:100',
            'nama_belanja' => 'required|string|max:255',
        ]);

        $validated['nama_kelompok'] = $this->kelompokLookup[$validated['kelompok']] ?? 'Belanja Modal';

        RekeningBelanja::create($validated);

        return redirect()->back()->with('success', 'Data Rekening Belanja SIPD berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $rekening = RekeningBelanja::findOrFail($id);

        $validated = $request->validate([
            'kelompok' => 'required|string|max:50',
            'kode_rek' => 'required|string|max:100',
            'nama_belanja' => 'required|string|max:255',
        ]);

        $validated['nama_kelompok'] = $this->kelompokLookup[$validated['kelompok']] ?? $rekening->nama_kelompok;

        $rekening->update($validated);

        return redirect()->back()->with('success', 'Data Rekening Belanja SIPD berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rekening = RekeningBelanja::findOrFail($id);
        $rekening->delete();

        return redirect()->back()->with('success', 'Data Rekening Belanja SIPD berhasil dihapus.');
    }
}

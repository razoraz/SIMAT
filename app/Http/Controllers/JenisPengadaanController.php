<?php

namespace App\Http\Controllers;

use App\Models\JenisPengadaan;
use Illuminate\Http\Request;

class JenisPengadaanController extends Controller
{
    public function index(Request $request)
    {
        // Seed initial default data if table is empty
        if (JenisPengadaan::count() === 0) {
            $defaultData = [
                [
                    'program_kode' => '0.00.01',
                    'program_nama' => 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
                    'kegiatan_kode' => '0.00.01.2.10',
                    'kegiatan_nama' => 'Peningkatan Pelayanan BLUD',
                    'sub_kegiatan_kode' => '0.00.01.2.10.0001',
                    'sub_kegiatan_nama' => 'Pelayanan dan Penunjang Pelayanan BLUD',
                    'keterangan' => 'Alokasi pengadaan operasional, sarana dan prasarana penunjang BLUD RSUD Dr. H. Koesnandi',
                ],
                [
                    'program_kode' => '0.00.01',
                    'program_nama' => 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
                    'kegiatan_kode' => '0.00.01.2.10',
                    'kegiatan_nama' => 'Peningkatan Pelayanan BLUD',
                    'sub_kegiatan_kode' => '0.00.01.2.10.0002',
                    'sub_kegiatan_nama' => 'Pengadaan Sarana dan Prasarana Pendukung Fasilitas Pelayanan Kesehatan',
                    'keterangan' => 'Belanja modal alat medis ICU, Bed Patient, Instalasi Gas Medis dan Genset Cadangan',
                ],
                [
                    'program_kode' => '1.02.02',
                    'program_nama' => 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat',
                    'kegiatan_kode' => '1.02.02.2.02',
                    'kegiatan_nama' => 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKP dan UKM Rujukan',
                    'sub_kegiatan_kode' => '1.02.02.2.02.0005',
                    'sub_kegiatan_nama' => 'Pembangunan / Renovasi Gedung Rumah Sakit dan Sarana Penunjang',
                    'keterangan' => 'Alokasi APBD/DAK untuk pekerjaan fisik renovasi paviliun dan gedung poliklinik',
                ],
                [
                    'program_kode' => '1.02.02',
                    'program_nama' => 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat',
                    'kegiatan_kode' => '1.02.02.2.02',
                    'kegiatan_nama' => 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKP dan UKM Rujukan',
                    'sub_kegiatan_kode' => '1.02.02.2.02.0012',
                    'sub_kegiatan_nama' => 'Pengadaan Alat Kesehatan / Alat Penunjang Medik Fasilitas Pelayanan Kesehatan',
                    'keterangan' => 'Pengadaan CT-Scan 128 Slice, USG Doppler 4D, Radiologi & Alat Kamar Operasi (IBS)',
                ],
                [
                    'program_kode' => '1.02.03',
                    'program_nama' => 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan',
                    'kegiatan_kode' => '1.02.03.2.01',
                    'kegiatan_nama' => 'Pengembangan Mutu dan Akreditasi Fasilitas Pelayanan Kesehatan',
                    'sub_kegiatan_kode' => '1.02.03.2.01.0003',
                    'sub_kegiatan_nama' => 'Pengadaan Sistem Informasi Kesehatan & Software Manajemen SIMRS',
                    'keterangan' => 'Pengadaan lisensi server, software Rekam Medis Elektronik (RME) & Integrasi SatuSehat',
                ],
            ];

            foreach ($defaultData as $item) {
                JenisPengadaan::create($item);
            }
        }

        $query = JenisPengadaan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('program_kode', 'like', "%{$search}%")
                  ->orWhere('program_nama', 'like', "%{$search}%")
                  ->orWhere('kegiatan_kode', 'like', "%{$search}%")
                  ->orWhere('kegiatan_nama', 'like', "%{$search}%")
                  ->orWhere('sub_kegiatan_kode', 'like', "%{$search}%")
                  ->orWhere('sub_kegiatan_nama', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('program') && $request->program !== 'all') {
            $query->where('program_kode', $request->program);
        }

        $sipdList = $query->orderBy('program_kode')->orderBy('kegiatan_kode')->orderBy('sub_kegiatan_kode')->get();
        $uniquePrograms = JenisPengadaan::select('program_kode', 'program_nama')->distinct()->orderBy('program_kode')->get();
        $uniqueKegiatan = JenisPengadaan::select('kegiatan_kode', 'kegiatan_nama')->distinct()->orderBy('kegiatan_kode')->get();
        $totalCount   = JenisPengadaan::count();

        return view('pages.master_jenis_pengadaan', compact('sipdList', 'uniquePrograms', 'uniqueKegiatan', 'totalCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_kode'      => 'required|string|max:50',
            'program_nama'      => 'required|string|max:255',
            'kegiatan_kode'     => 'required|string|max:50',
            'kegiatan_nama'     => 'required|string|max:255',
            'sub_kegiatan_kode' => 'required|string|max:50',
            'sub_kegiatan_nama' => 'required|string|max:255',
            'keterangan'        => 'nullable|string',
        ]);

        JenisPengadaan::create($validated);

        return redirect()->back()->with('success', 'Data Jenis Pengadaan SIPD berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = JenisPengadaan::findOrFail($id);

        $validated = $request->validate([
            'program_kode'      => 'required|string|max:50',
            'program_nama'      => 'required|string|max:255',
            'kegiatan_kode'     => 'required|string|max:50',
            'kegiatan_nama'     => 'required|string|max:255',
            'sub_kegiatan_kode' => 'required|string|max:50',
            'sub_kegiatan_nama' => 'required|string|max:255',
            'keterangan'        => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->back()->with('success', 'Data Jenis Pengadaan SIPD berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = JenisPengadaan::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Data Jenis Pengadaan SIPD berhasil dihapus.');
    }
}

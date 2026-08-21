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
            (new \Database\Seeders\JenisPengadaanSeeder())->run();
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
                  ->orWhere('sub_kegiatan_nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('program') && $request->program !== 'all') {
            $prog = $request->program;
            $query->where(function ($q) use ($prog) {
                $q->where('program_kode', $prog)
                  ->orWhere('program_nama', 'like', "%{$prog}%");
            });
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

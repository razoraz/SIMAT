<?php

namespace App\Http\Controllers;

use App\Models\JenisPengadaan;
use Illuminate\Http\Request;

class JenisPengadaanController extends Controller
{
    public function index(Request $request)
    {
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

        $sipdList     = $query->orderBy('program_kode')->orderBy('kegiatan_kode')->orderBy('sub_kegiatan_kode')->get();
        $uniquePrograms = JenisPengadaan::select('program_kode', 'program_nama')->distinct()->orderBy('program_kode')->get();
        $totalCount   = JenisPengadaan::count();

        return view('pages.master_jenis_pengadaan', compact('sipdList', 'uniquePrograms', 'totalCount'));
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

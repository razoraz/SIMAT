<?php

namespace App\Http\Controllers;

use App\Models\JenisPengadaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JenisPengadaanController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisPengadaan::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
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
            $prog = trim($request->program);
            $query->where(function ($q) use ($prog) {
                $q->where('program_kode', $prog)
                  ->orWhere('program_nama', 'like', "%{$prog}%");
            });
        }

        $perPage = in_array((int)$request->input('per_page'), [10, 15, 25, 50, 100]) ? (int)$request->input('per_page') : 15;

        $sipdList = $query->withCount('astapBelanjaModals')
            ->orderBy('program_kode')
            ->orderBy('kegiatan_kode')
            ->orderBy('sub_kegiatan_kode')
            ->paginate($perPage)
            ->withQueryString();

        $uniquePrograms = JenisPengadaan::select('program_kode', 'program_nama')
            ->whereNotNull('program_kode')->where('program_kode', '!=', '')
            ->whereNotNull('program_nama')->where('program_nama', '!=', '')
            ->distinct()
            ->orderBy('program_kode')
            ->get();

        $uniqueKegiatan = JenisPengadaan::select('program_kode', 'kegiatan_kode', 'kegiatan_nama')
            ->whereNotNull('kegiatan_kode')->where('kegiatan_kode', '!=', '')
            ->whereNotNull('kegiatan_nama')->where('kegiatan_nama', '!=', '')
            ->distinct()
            ->orderBy('kegiatan_kode')
            ->get();

        $totalCount = JenisPengadaan::count();

        return view('pages.master.jenis_pengadaan.index', compact('sipdList', 'uniquePrograms', 'uniqueKegiatan', 'totalCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_kode'      => 'required|string|max:50',
            'program_nama'      => 'required|string|max:255',
            'kegiatan_kode'     => 'required|string|max:50',
            'kegiatan_nama'     => 'required|string|max:255',
            'sub_kegiatan_kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('jenis_pengadaans', 'sub_kegiatan_kode'),
            ],
            'sub_kegiatan_nama' => 'required|string|max:255',
        ], [
            'sub_kegiatan_kode.unique' => 'Kode Sub Kegiatan [:input] sudah terdaftar dalam sistem.',
            'required' => 'Kolom :attribute wajib diisi.',
        ]);

        DB::transaction(function () use ($validated) {
            JenisPengadaan::create($validated);
        });

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
            'sub_kegiatan_kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('jenis_pengadaans', 'sub_kegiatan_kode')->ignore($item->id),
            ],
            'sub_kegiatan_nama' => 'required|string|max:255',
        ], [
            'sub_kegiatan_kode.unique' => 'Kode Sub Kegiatan [:input] sudah terdaftar pada data pengadaan lain.',
            'required' => 'Kolom :attribute wajib diisi.',
        ]);

        DB::transaction(function () use ($item, $validated) {
            $item->update($validated);
        });

        return redirect()->back()->with('success', 'Data Jenis Pengadaan SIPD berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = JenisPengadaan::withCount('astapBelanjaModals')->findOrFail($id);

        if ($item->astap_belanja_modals_count > 0) {
            return redirect()->back()->with('error', "Gagal menghapus: Sub Kegiatan [{$item->sub_kegiatan_kode} - {$item->sub_kegiatan_nama}] masih terhubung dengan {$item->astap_belanja_modals_count} data belanja modal aset aktif. Hapus atau alihkan relasi belanja modal aset terlebih dahulu.");
        }

        DB::transaction(function () use ($item) {
            $item->delete();
        });

        return redirect()->back()->with('success', "Data Jenis Pengadaan [{$item->sub_kegiatan_kode}] berhasil dihapus.");
    }
}

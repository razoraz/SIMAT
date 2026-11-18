<?php

namespace App\Http\Controllers;

use App\Models\RekeningBelanja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $query = RekeningBelanja::withCount('astapBelanjaModals');

        if ($request->filled('search')) {
            $search = trim($request->search);
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

        $perPage = in_array((int)$request->input('per_page'), [10, 15, 25, 50, 100])
            ? (int)$request->input('per_page')
            : 15;

        $rekeningList = $query->orderBy('kelompok')->orderBy('kode_rek')->paginate($perPage)->withQueryString();
        
        $defaultKelompok = [
            ['kelompok' => '5.2.01', 'nama_kelompok' => 'Belanja Modal Tanah'],
            ['kelompok' => '5.2.02', 'nama_kelompok' => 'Belanja Modal Peralatan dan Mesin'],
            ['kelompok' => '5.2.03', 'nama_kelompok' => 'Belanja Modal Gedung dan Bangunan'],
            ['kelompok' => '5.2.04', 'nama_kelompok' => 'Belanja Modal Jalan, Jaringan dan Irigasi'],
            ['kelompok' => '5.2.05', 'nama_kelompok' => 'Belanja Modal Aset Tetap Lainnya'],
            ['kelompok' => '5.2.06', 'nama_kelompok' => 'Belanja Modal Aset Tidak Berwujud'],
        ];

        $dbKelompok = RekeningBelanja::select('kelompok', 'nama_kelompok')
            ->whereNotNull('kelompok')->where('kelompok', '!=', '')
            ->whereNotNull('nama_kelompok')->where('nama_kelompok', '!=', '')
            ->distinct()
            ->get()
            ->toArray();

        $uniqueKelompok = collect(array_merge($defaultKelompok, $dbKelompok))
            ->unique('kelompok')
            ->sortBy('kelompok')
            ->values();

        $totalCount = RekeningBelanja::count();

        return view('pages.master.rekening_belanja.index', compact('rekeningList', 'uniqueKelompok', 'totalCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelompok' => 'required|string|max:50',
            'kode_rek' => [
                'required',
                'string',
                'max:100',
                Rule::unique('rekening_belanjas', 'kode_rek'),
            ],
            'nama_belanja' => 'required|string|max:255',
            'nama_kelompok' => 'nullable|string|max:255',
        ], [
            'kode_rek.unique' => 'Kode rekening belanja sudah terdaftar.',
            'kode_rek.required' => 'Kode rekening belanja wajib diisi.',
            'nama_belanja.required' => 'Nama belanja pengadaan wajib diisi.',
            'kelompok.required' => 'Kelompok akun belanja wajib diisi.',
        ]);

        $validated['nama_kelompok'] = !empty($request->nama_kelompok)
            ? trim($request->nama_kelompok)
            : ($this->kelompokLookup[$validated['kelompok']] ?? 'Belanja Modal');

        DB::transaction(function () use ($validated) {
            RekeningBelanja::create($validated);
        });

        return redirect()->back()->with('success', 'Data Rekening Belanja SIPD berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $rekening = RekeningBelanja::findOrFail($id);

        $validated = $request->validate([
            'kelompok' => 'required|string|max:50',
            'kode_rek' => [
                'required',
                'string',
                'max:100',
                Rule::unique('rekening_belanjas', 'kode_rek')->ignore($rekening->id),
            ],
            'nama_belanja' => 'required|string|max:255',
            'nama_kelompok' => 'nullable|string|max:255',
        ], [
            'kode_rek.unique' => 'Kode rekening belanja sudah digunakan pada akun lain.',
            'kode_rek.required' => 'Kode rekening belanja wajib diisi.',
            'nama_belanja.required' => 'Nama belanja pengadaan wajib diisi.',
            'kelompok.required' => 'Kelompok akun belanja wajib diisi.',
        ]);

        $validated['nama_kelompok'] = !empty($request->nama_kelompok)
            ? trim($request->nama_kelompok)
            : ($this->kelompokLookup[$validated['kelompok']] ?? $rekening->nama_kelompok);

        DB::transaction(function () use ($rekening, $validated) {
            $rekening->update($validated);
        });

        return redirect()->back()->with('success', 'Data Rekening Belanja SIPD berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rekening = RekeningBelanja::withCount('astapBelanjaModals')->findOrFail($id);

        if ($rekening->astap_belanja_modals_count > 0) {
            return redirect()->back()->with('error', "Gagal menghapus! Rekening belanja [{$rekening->kode_rek}] {$rekening->nama_belanja} sedang digunakan oleh {$rekening->astap_belanja_modals_count} data aset ASTAP.");
        }

        DB::transaction(function () use ($rekening) {
            $rekening->delete();
        });

        return redirect()->back()->with('success', 'Data Rekening Belanja SIPD berhasil dihapus.');
    }
}

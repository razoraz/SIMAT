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
        // Seed default data if table is empty
        if (RekeningBelanja::count() === 0) {
            (new \Database\Seeders\RekeningBelanjaSeeder())->run();
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

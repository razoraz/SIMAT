<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    /**
     * Tampilkan Halaman Katalog Unit & Paviliun RSUD.
     */
    public function index()
    {
        $units = Unit::with('user')->orderBy('id', 'asc')->get()->map(function ($u, $index) {
            return [
                'id' => $u->id,
                'kode' => $u->kode_unit ?: ('UNIT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)),
                'nama' => $u->nama,
                'tipe' => $u->tipe ?: 'Rawat Inap & Paviliun',
                'kepala' => $u->kepala,
                'nip' => $u->nip ?: '-',
                'email' => $u->email ?: ($u->user->email ?? '-'),
                'id_aset' => $u->id_aset ?? [],
                'total_aset' => $u->total_aset ?? 0,
                'total_nilai' => $u->total_nilai ?? 'Rp 0',
                'assets' => []
            ];
        });

        return view('pages.unit_paviliun', compact('units'));
    }

    /**
     * Form Tambah Unit Baru.
     */
    public function create()
    {
        $nextKode = Unit::generateNextKode();
        return view('pages.form_unit_paviliun', compact('nextKode'));
    }

    /**
     * Simpan Unit Baru ke Database (Otomatis Buat Akun Sub Admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_unit' => 'nullable|string|max:50',
            'nama' => 'required|string|max:150|unique:units,nama',
            'tipe' => 'nullable|string|max:100',
            'kepala' => 'required|string|max:150',
            'nip' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
        ]);

        if (empty($validated['kode_unit'])) {
            $validated['kode_unit'] = Unit::generateNextKode();
        }

        $validated['id_aset'] = [];
        $validated['total_aset'] = 0;
        $validated['total_nilai'] = 'Rp 0';

        // Simpan Unit -> Model Hook otomatis membuat Akun Sub Admin di tabel users
        $unit = Unit::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Unit {$unit->nama} berhasil didaftarkan dan Akun Sub Admin ({$unit->kepala}) telah otomatis dibuat!",
                'unit' => $unit
            ]);
        }

        return redirect()->route('unit.index')->with('success', "Unit {$unit->nama} berhasil disimpan.");
    }

    /**
     * Form Ubah Unit.
     */
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('pages.form_unit_paviliun', compact('unit', 'id'));
    }

    /**
     * Update Data Unit (Sinkron ke Akun Sub Admin Terkait).
     */
    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:150|unique:units,nama,' . $unit->id,
            'tipe' => 'nullable|string|max:100',
            'kepala' => 'required|string|max:150',
            'nip' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
        ]);

        $unit->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data Unit {$unit->nama} dan Akun Sub Admin terkait berhasil diperbarui!",
                'unit' => $unit
            ]);
        }

        return redirect()->route('unit.index')->with('success', "Unit {$unit->nama} berhasil diperbarui.");
    }

    /**
     * Hapus Unit.
     */
    public function destroy(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        $nama = $unit->nama;
        $unit->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Unit {$nama} berhasil dihapus."
            ]);
        }

        return redirect()->route('unit.index')->with('success', "Unit {$nama} berhasil dihapus.");
    }
}

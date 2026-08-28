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
            // 1. Cari register ASTAP yang terhubung via unit_id atau ruang_pemegang
            $registers = \App\Models\AstapRegister::with(['astap.jenisAstap'])
                ->where(function($q) use ($u) {
                    $q->where('unit_id', $u->id)
                      ->orWhere(function($q2) use ($u) {
                          $q2->whereNotNull('ruang_pemegang')
                             ->where('ruang_pemegang', '!=', '')
                             ->where(function($q3) use ($u) {
                                 $q3->where('ruang_pemegang', $u->nama)
                                    ->orWhere('ruang_pemegang', 'LIKE', '%' . $u->nama . '%');
                             });
                      });
                })
                ->get();

            // 2. Ambil ASTAP yang terhubung langsung via unit_id (jika ada register yang belum masuk)
            $astapDirectIds = \App\Models\Astap::where('unit_id', $u->id)
                ->whereNotIn('id', $registers->pluck('astap_id')->filter()->unique())
                ->pluck('id');

            $directRegisters = collect();
            if ($astapDirectIds->isNotEmpty()) {
                $directRegisters = \App\Models\AstapRegister::with(['astap.jenisAstap'])
                    ->whereIn('astap_id', $astapDirectIds)
                    ->get();
            }

            $allRegisters = $registers->concat($directRegisters)->unique('id');

            $assets = [];
            $totalNilaiSum = 0;

            foreach ($allRegisters as $reg) {
                $astap = $reg->astap;
                if (!$astap) continue;

                $hargaSatuan = (float) ($astap->harga_satuan ?: ($astap->jumlah_volume > 0 ? ($astap->total_realisasi / $astap->jumlah_volume) : 0));
                $totalNilaiSum += $hargaSatuan;

                $assets[] = [
                    'id'            => $reg->id,
                    'astap_id'      => $astap->id,
                    'nama'          => $astap->nama_barang ?? 'Barang Inventaris',
                    'kode'          => $reg->nibar ?: ($reg->no_register ?: ($astap->kode_108 ?? '-')),
                    'nibar'         => $reg->nibar ?: ($reg->no_register ?: '-'),
                    'merk'          => $astap->merk_type ?: (is_array($astap->spesifikasi_json) && isset($astap->spesifikasi_json['merk']) ? $astap->spesifikasi_json['merk'] : '-'),
                    'no_seri'       => $reg->no_seri ?: (is_array($astap->spesifikasi_json) && isset($astap->spesifikasi_json['no_pabrik']) ? $astap->spesifikasi_json['no_pabrik'] : '-'),
                    'kondisi'       => $reg->kondisi ?: 'Baik',
                    'tahun'         => $astap->tahun_perolehan ?: '-',
                    'harga'         => $hargaSatuan,
                    'harga_fmt'     => 'Rp ' . number_format($hargaSatuan, 0, ',', '.'),
                    'category'      => $astap->jenisAstap ? $astap->jenisAstap->kategori : 'ASTAP',
                    'ruang'         => $reg->ruang_pemegang ?: $u->nama,
                    'tanggal_masuk' => $reg->created_at ? $reg->created_at->format('d/m/Y') : '-'
                ];
            }

            $totalAsetCount = count($assets);
            $totalNilaiFmt = 'Rp ' . number_format($totalNilaiSum, 0, ',', '.');

            // Simpan sinkronisasi ke tabel database units
            $u->update([
                'total_aset'  => $totalAsetCount,
                'total_nilai' => $totalNilaiFmt,
                'id_aset'     => array_column($assets, 'id'),
            ]);

            return [
                'id'          => $u->id,
                'kode'        => $u->kode_unit ?: ('UNIT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)),
                'nama'        => $u->nama,
                'tipe'        => $u->tipe ?: 'Rawat Inap & Paviliun',
                'kepala'      => $u->kepala,
                'nip'         => $u->nip ?: '-',
                'email'       => $u->email ?: ($u->user->email ?? '-'),
                'id_aset'     => array_column($assets, 'id'),
                'total_aset'  => $totalAsetCount,
                'total_nilai' => $totalNilaiFmt,
                'assets'      => $assets
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

        session()->flash('success', "Unit {$unit->nama} berhasil ditambahkan.");
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

        session()->flash('success', "Unit {$unit->nama} berhasil diperbarui.");
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

        session()->flash('success', "Unit {$nama} berhasil dihapus.");
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Unit {$nama} berhasil dihapus."
            ]);
        }

        return redirect()->route('unit.index')->with('success', "Unit {$nama} berhasil dihapus.");
    }
}

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
                    'nilai'         => $hargaSatuan,
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

    /**
     * Halaman Khusus Lembar Kartu Inventaris Ruangan (KIR)
     */
    public function kir(Request $request)
    {
        $user = Auth::user();
        $units = Unit::orderBy('nama', 'asc')->get();

        // Tentukan unit yang ditampilkan:
        // Jika sub_admin, utamakan unit yang ditugaskan kepada sub_admin tersebut
        $selectedUnitId = $request->get('unit_id');
        if ($user->role === 'sub_admin' && $user->unit_id) {
            $selectedUnitId = $user->unit_id;
        } elseif (!$selectedUnitId) {
            $selectedUnitId = $user->unit_id ?: ($units->first()?->id ?? null);
        }

        $currentUnit = $selectedUnitId ? Unit::with('user')->find($selectedUnitId) : null;

        $assets = [];
        $totalNilaiSum = 0;
        $kondisiBaik = 0;
        $kondisiKurangBaik = 0;
        $kondisiRusakRingan = 0;
        $kondisiRusakBerat = 0;

        if ($currentUnit) {
            $registers = \App\Models\AstapRegister::with(['astap.jenisAstap'])
                ->where(function($q) use ($currentUnit) {
                    $q->where('unit_id', $currentUnit->id)
                      ->orWhere(function($q2) use ($currentUnit) {
                          $q2->whereNotNull('ruang_pemegang')
                             ->where('ruang_pemegang', '!=', '')
                             ->where(function($q3) use ($currentUnit) {
                                 $q3->where('ruang_pemegang', $currentUnit->nama)
                                    ->orWhere('ruang_pemegang', 'LIKE', '%' . $currentUnit->nama . '%');
                             });
                      });
                })
                ->get();

            $astapDirectIds = \App\Models\Astap::where('unit_id', $currentUnit->id)
                ->whereNotIn('id', $registers->pluck('astap_id')->filter()->unique())
                ->pluck('id');

            $directRegisters = collect();
            if ($astapDirectIds->isNotEmpty()) {
                $directRegisters = \App\Models\AstapRegister::with(['astap.jenisAstap'])
                    ->whereIn('astap_id', $astapDirectIds)
                    ->get();
            }

            $allRegisters = $registers->concat($directRegisters)->unique('id');

            foreach ($allRegisters as $reg) {
                $astap = $reg->astap;
                if (!$astap) continue;

                $hargaSatuan = (float) ($astap->harga_satuan ?: ($astap->jumlah_volume > 0 ? ($astap->total_realisasi / $astap->jumlah_volume) : 0));
                $totalNilaiSum += $hargaSatuan;

                $kondisi = $reg->kondisi ?: 'Baik';
                if ($kondisi === 'Baik') {
                    $kondisiBaik++;
                } elseif ($kondisi === 'Kurang Baik' || $kondisi === 'Rusak Ringan') {
                    $kondisiKurangBaik++;
                } else {
                    $kondisiRusakBerat++;
                }

                $spec = is_array($astap->spesifikasi_json)
                    ? $astap->spesifikasi_json
                    : (json_decode($astap->spesifikasi_json ?? '', true) ?? []);

                $merk = $astap->merk_type ?: ($spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? '-')));
                $noSeri = $reg->no_seri ?: ($spec['no_pabrik'] ?? ($spec['no_rangka'] ?? ($spec['no_mesin'] ?? '-')));
                $bahan = $spec['bahan'] ?? ($spec['material'] ?? '-');
                $ukuran = $spec['ukuran'] ?? ($spec['kapasitas'] ?? '-');
                $kategoriKib = $astap->kategori_kib ?: ($astap->jenisAstap->kategori ?? 'KIB B');

                $spkTanggal = $astap->spk_tanggal ? (\Carbon\Carbon::parse($astap->spk_tanggal)->format('d/m/Y')) : '-';
                $sp2dTanggal = $astap->sp2d_tanggal ? (\Carbon\Carbon::parse($astap->sp2d_tanggal)->format('d/m/Y')) : '-';

                $assets[] = [
                    'id'               => $reg->id,
                    'astap_id'         => $astap->id,
                    'nama'             => $astap->nama_barang ?? 'Barang Inventaris',
                    'kode_108'         => $astap->kode_108 ?: ($astap->kode_barang ?: '-'),
                    'kode_barang'      => $astap->kode_barang ?: ($astap->kode_108 ?: '-'),
                    'kode'             => $reg->nibar ?: ($reg->no_register ?: ($astap->kode_108 ?? '-')),
                    'nibar'            => $reg->nibar ?: ($reg->no_register ?: '-'),
                    'merk'             => $merk,
                    'tipe'             => $astap->type ?: ($spec['type'] ?? '-'),
                    'no_seri'          => $noSeri,
                    'no_pabrik'        => $spec['no_pabrik'] ?? ($reg->no_seri ?: '-'),
                    'no_rangka'        => $spec['no_rangka'] ?? '-',
                    'no_mesin'         => $spec['no_mesin'] ?? '-',
                    'no_polisi'        => $spec['no_polisi'] ?? '-',
                    'bahan'            => $bahan,
                    'ukuran'           => $ukuran,
                    'satuan'           => $astap->satuan ?: 'Unit',
                    'volume'           => $astap->jumlah_volume ?: 1,
                    'kondisi'          => ($kondisi === 'Rusak Ringan') ? 'Kurang Baik' : $kondisi,
                    'tahun'            => $astap->tahun_perolehan ?: '-',
                    'harga'            => $hargaSatuan,
                    'harga_fmt'        => 'Rp ' . number_format($hargaSatuan, 0, ',', '.'),
                    'total_realisasi'  => (float) ($astap->total_realisasi ?: $hargaSatuan),
                    'total_realisasi_fmt' => 'Rp ' . number_format((float) ($astap->total_realisasi ?: $hargaSatuan), 0, ',', '.'),
                    'kategori'         => $astap->jenisAstap ? ($astap->jenisAstap->nama_jenis ?: $astap->jenisAstap->kategori) : 'ASTAP',
                    'kategori_kib'     => $kategoriKib,
                    'ruang'            => $reg->ruang_pemegang ?: $currentUnit->nama,
                    'penyedia'         => $astap->penyedia_nama ?: ($spec['penyedia'] ?? '-'),
                    'spk_nomor'        => $astap->spk_nomor ?: '-',
                    'spk_tanggal'      => $spkTanggal,
                    'sp2d_nomor'       => $astap->sp2d_nomor ?: '-',
                    'sp2d_tanggal'     => $sp2dTanggal,
                    'bast_nomor'       => $astap->bast_dokumen_nomor ?: '-',
                    'alamat'           => $astap->alamat_barang ?: '-',
                    'tanggal_masuk'    => $reg->created_at ? $reg->created_at->format('d/m/Y') : '-',
                    'spesifikasi_json' => $spec
                ];
            }
        }

        $totalAsetCount = count($assets);
        $totalNilaiFmt = 'Rp ' . number_format($totalNilaiSum, 0, ',', '.');
        $totalRusak = $kondisiKurangBaik + $kondisiRusakBerat;

        return view('pages.lembar_kir', [
            'units'              => $units,
            'currentUnit'        => $currentUnit,
            'assets'             => $assets,
            'totalAsetCount'     => $totalAsetCount,
            'totalNilaiFmt'      => $totalNilaiFmt,
            'kondisiBaik'        => $kondisiBaik,
            'kondisiKurangBaik'  => $kondisiKurangBaik,
            'kondisiRusakRingan' => 0,
            'kondisiRusakBerat'  => $kondisiRusakBerat,
            'totalRusak'         => $totalRusak,
            'user'               => $user,
        ]);
    }

    /**
     * Update kondisi fisik aset register dari Lembar KIR Ruangan
     */
    public function updateKondisi(Request $request, $id)
    {
        $reg = \App\Models\AstapRegister::find($id);
        if (!$reg) {
            return response()->json([
                'success' => false,
                'message' => 'Register aset tidak ditemukan.'
            ], 404);
        }

        $validated = $request->validate([
            'kondisi' => 'required|in:Baik,Kurang Baik,Rusak Berat'
        ]);

        $kondisiLama = $reg->kondisi;
        $reg->kondisi = $validated['kondisi'];
        $reg->save();

        return response()->json([
            'success'      => true,
            'message'      => "Kondisi aset berhasil diubah dari '{$kondisiLama}' menjadi '{$reg->kondisi}'.",
            'kondisi'      => $reg->kondisi,
            'id'           => $reg->id,
            'updated_at'   => $reg->updated_at ? $reg->updated_at->format('d/m/Y H:i') : date('d/m/Y H:i')
        ]);
    }
}

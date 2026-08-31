<?php

namespace App\Http\Controllers;

use App\Models\AstapMutasi;
use App\Models\AstapMutasiRegister;
use App\Models\AstapRegister;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MutasiController extends Controller
{
    /**
     * Tampilkan daftar mutasi aset.
     */
    public function index()
    {
        $rawMutasis = AstapMutasi::with(['items.register.astap', 'items.register.unit'])
            ->latest('tanggal_mutasi')
            ->latest('id')
            ->get();

        $mutasis = $rawMutasis->map(function ($m) {
            $firstItem = $m->items->first();
            $itemCount = $m->items->count();
            $firstRegister = $firstItem?->register ?? $m->register;
            $firstAstap = $firstRegister?->astap;

            $itemSummary = $firstAstap?->nama_barang ?? '(Aset Tanpa Nama)';
            if ($itemCount > 1) {
                $itemSummary .= ' (+' . ($itemCount - 1) . ' barang lainnya)';
            }

            $itemsMapped = $m->items->map(function ($it, $idx) {
                $r = $it->register;
                return [
                    'no'          => $idx + 1,
                    'register_id' => $it->astap_register_id,
                    'nibar'       => $r?->nibar ?? '-',
                    'nama_barang' => $r?->astap?->nama_barang ?? '-',
                    'kode_108'    => $r?->astap?->kode_108 ?? ($r?->kode_108 ?? '-'),
                    'kondisi'     => $it->kondisi ?? ($r?->kondisi ?? 'Baik'),
                    'kategori'    => $r?->astap?->category ?? 'ASTAP',
                    'satuan'      => $r?->astap?->satuan ?? 'Unit',
                    'volume'      => 1,
                ];
            })->values()->toArray();

            // Jika items kosong (legacy single), buat 1 item fallback
            if (empty($itemsMapped) && $firstRegister) {
                $itemsMapped = [[
                    'no'          => 1,
                    'register_id' => $firstRegister->id,
                    'nibar'       => $firstRegister->nibar ?? '-',
                    'nama_barang' => $firstAstap?->nama_barang ?? '-',
                    'kode_108'    => $firstAstap?->kode_108 ?? ($firstRegister->kode_108 ?? '-'),
                    'kondisi'     => $m->kondisi ?? ($firstRegister->kondisi ?? 'Baik'),
                    'kategori'    => $firstAstap?->category ?? 'ASTAP',
                    'satuan'      => $firstAstap?->satuan ?? 'Unit',
                    'volume'      => 1,
                ]];
                $itemCount = 1;
            }

            return [
                'id'                      => $m->id,
                'kode'                    => $m->nomor_bamb,
                'bast_nomor'              => $m->nomor_bamb,
                'jenis'                   => $m->jenis_mutasi,
                'nama'                    => $itemSummary,
                'item_count'              => $itemCount,
                'items'                   => $itemsMapped,
                'kode_barang'             => $firstRegister?->nibar ?? '-',
                'kode_108'                => $firstAstap?->kode_108 ?? ($firstRegister?->kode_108 ?? '-'),
                'kondisi'                 => $firstItem?->kondisi ?? ($m->kondisi ?? ($firstRegister?->kondisi ?? 'Baik')),
                'asal'                    => $m->ruangan_asal,
                'tujuan'                  => $m->ruangan_tujuan,
                'tgl'                     => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('d M Y') : '-',
                'tgl_raw'                 => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y-m-d') : null,
                'pemohon'                 => $m->penanggung_jawab_asal,
                'penerima_pj'             => $m->penanggung_jawab_tujuan,
                'pj_asal_nama'            => $m->penanggung_jawab_asal,
                'pj_asal_nip'             => '-',
                'pj_asal_jabatan'         => 'Penanggung Jawab ' . $m->ruangan_asal,
                'pj_tujuan_nama'          => $m->penanggung_jawab_tujuan,
                'pj_tujuan_nip'           => '-',
                'pj_tujuan_jabatan'       => 'Penanggung Jawab ' . $m->ruangan_tujuan,
                'persetujuan_pengirim'    => (bool) $m->persetujuan_pengirim,
                'persetujuan_penerima'    => (bool) $m->persetujuan_penerima,
                'persetujuan_admin'       => (bool) $m->persetujuan_admin,
                'status'                  => $m->status,
                'keterangan'              => $m->alasan_mutasi,
                'catatan_penerima'        => $m->catatan_penerima,
                'alasan_penolakan'        => $m->alasan_penolakan,
                'hari'                    => $m->tanggal_mutasi ? $m->tanggal_mutasi->translatedFormat('l') : 'Hari ini',
                'tanggal_angka'           => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('d') : date('d'),
                'bulan'                   => $m->tanggal_mutasi ? $m->tanggal_mutasi->translatedFormat('F') : date('F'),
                'tahun'                   => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y') : date('Y'),
            ];
        });

        $units = Unit::orderBy('nama')->get();

        return view('pages.mutasi_aset', compact('mutasis', 'units'));
    }

    /**
     * Tampilkan form pengajuan mutasi baru.
     */
    public function create()
    {
        $units = Unit::orderBy('nama')->get(['id', 'nama', 'kepala']);
        $rawRegisters = AstapRegister::with('astap', 'unit')
            ->whereNotNull('nibar')
            ->orderBy('id')
            ->get();
        $registers = $rawRegisters->map(function ($r) {
            return [
                'id'          => $r->id,
                'nibar'       => $r->nibar ?? '-',
                'nama_barang' => $r->astap?->nama_barang ?? '-',
                'kondisi'     => $r->kondisi ?? 'Baik',
                'unit_nama'   => $r->unit?->nama ?? ($r->ruang_pemegang ?? '-'),
                'unit_kepala' => $r->unit?->kepala ?? '-',
            ];
        });

        return view('pages.form_mutasi_aset', compact('units', 'registers'));
    }

    /**
     * Simpan pengajuan mutasi ke database.
     * Menggunakan pola Header-Detail (1 Berita Acara BAMB memuat N barang).
     */
    public function store(Request $request)
    {
        $request->validate([
            'astap_register_id'       => 'nullable|exists:astap_registers,id',
            'astap_register_ids'      => 'nullable|array',
            'astap_register_ids.*'    => 'exists:astap_registers,id',
            'jenis_mutasi'            => 'required|in:Ajukan Mutasi,Pemindahan,Perbaikan,Minta Mutasi,Pengembalian,Penghapusan',
            'tanggal_mutasi'          => 'required|date',
            'ruangan_asal'            => 'required|string|max:255',
            'ruangan_tujuan'          => 'required|string|max:255|different:ruangan_asal',
            'penanggung_jawab_asal'   => 'required|string|max:255',
            'penanggung_jawab_tujuan' => 'required|string|max:255',
            'alasan_mutasi'           => 'required|string|max:2000',
            'catatan_penerima'        => 'nullable|string|max:1000',
        ], [
            'ruangan_tujuan.different' => 'Ruangan tujuan harus berbeda dengan ruangan asal.',
        ]);

        $registerIds = $request->input('astap_register_ids', []);
        if (empty($registerIds) && $request->astap_register_id) {
            $registerIds = [$request->astap_register_id];
        }

        if (empty($registerIds)) {
            return back()->withErrors(['astap_register_id' => 'Silakan pilih minimal 1 barang aset yang akan dimutasi.']);
        }

        $kondisiBaru = $request->input('kondisi_baru', []);

        // 1. Generate 1 nomor Berita Acara BAMB unik (format 7 digit: MTS-2026-0000001)
        $year  = date('Y', strtotime($request->tanggal_mutasi));
        $count = AstapMutasi::whereYear('tanggal_mutasi', $year)->count();
        $seq   = $count + 1;
        do {
            $nomor = 'MTS-' . $year . '-' . str_pad($seq, 7, '0', STR_PAD_LEFT);
            $exists = AstapMutasi::where('nomor_bamb', $nomor)->exists();
            if ($exists) {
                $seq++;
            }
        } while ($exists);

        $firstRegId = $registerIds[0] ?? null;

        // 2. Buat 1 baris Dokumen Berita Acara (Header)
        $mutasi = AstapMutasi::create([
            'astap_register_id'        => $firstRegId,
            'nomor_bamb'               => $nomor,
            'tanggal_mutasi'           => $request->tanggal_mutasi,
            'jenis_mutasi'             => $request->jenis_mutasi,
            'kondisi'                  => 'Baik',
            'ruangan_asal'             => $request->ruangan_asal,
            'ruangan_tujuan'           => $request->ruangan_tujuan,
            'penanggung_jawab_asal'    => $request->penanggung_jawab_asal,
            'penanggung_jawab_tujuan'  => $request->penanggung_jawab_tujuan,
            'alasan_mutasi'            => $request->alasan_mutasi,
            'catatan_penerima'         => $request->catatan_penerima,
            'persetujuan_pengirim'     => true,
            'tgl_persetujuan_pengirim' => now(),
            'status'                   => 'Menunggu Persetujuan Penerima',
        ]);

        // 3. Masukkan seluruh item register yang dimutasi ke tabel rincian astap_mutasi_registers
        $createdCount = 0;
        foreach ($registerIds as $regId) {
            $regObj = AstapRegister::find($regId);
            if (isset($kondisiBaru[$regId]) && in_array($kondisiBaru[$regId], ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'])) {
                if ($regObj && $regObj->kondisi !== $kondisiBaru[$regId]) {
                    $regObj->update(['kondisi' => $kondisiBaru[$regId]]);
                }
            }
            $kondisiSaatMutasi = $regObj ? $regObj->kondisi : 'Baik';

            AstapMutasiRegister::create([
                'astap_mutasi_id'   => $mutasi->id,
                'astap_register_id' => $regId,
                'kondisi'           => $kondisiSaatMutasi,
            ]);
            $createdCount++;
        }

        $msg = $createdCount > 1
            ? "Pengajuan mutasi {$request->jenis_mutasi} sebanyak {$createdCount} barang aset berhasil dibuat dalam 1 Berita Acara ({$nomor})! Menunggu persetujuan penerima."
            : "Pengajuan mutasi {$request->jenis_mutasi} ({$nomor}) berhasil dikirim! Menunggu persetujuan penerima.";

        return redirect()->route('mutasi.index')->with('success', $msg);
    }

    /**
     * Tampilkan form edit mutasi.
     */
    public function edit($id)
    {
        $mutasi = AstapMutasi::with(['items.register.astap', 'items.register.unit', 'register.astap'])->findOrFail($id);
        $units  = Unit::orderBy('nama')->get(['id', 'nama', 'kepala']);

        $relatedRegisterIds = $mutasi->items->pluck('astap_register_id')->filter()->unique()->values()->toArray();
        if (empty($relatedRegisterIds) && $mutasi->astap_register_id) {
            $relatedRegisterIds = [$mutasi->astap_register_id];
        }

        $rawRegisters = AstapRegister::with('astap', 'unit')
            ->whereNotNull('nibar')
            ->orderBy('id')
            ->get();
        $registers = $rawRegisters->map(function ($r) {
            return [
                'id'          => $r->id,
                'nibar'       => $r->nibar ?? '-',
                'nama_barang' => $r->astap?->nama_barang ?? '-',
                'kondisi'     => $r->kondisi ?? 'Baik',
                'unit_nama'   => $r->unit?->nama ?? ($r->ruang_pemegang ?? '-'),
                'unit_kepala' => $r->unit?->kepala ?? '-',
            ];
        });

        return view('pages.form_mutasi_aset', compact('mutasi', 'units', 'registers', 'relatedRegisterIds'));
    }

    /**
     * Update data mutasi.
     */
    public function update(Request $request, $id)
    {
        $mutasi = AstapMutasi::findOrFail($id);

        $request->validate([
            'astap_register_ids'      => 'nullable|array',
            'astap_register_ids.*'    => 'exists:astap_registers,id',
            'astap_register_id'       => 'nullable|exists:astap_registers,id',
            'jenis_mutasi'            => 'required|in:Ajukan Mutasi,Pemindahan,Perbaikan,Minta Mutasi,Pengembalian,Penghapusan',
            'tanggal_mutasi'          => 'nullable|date',
            'ruangan_asal'            => 'required|string|max:255',
            'ruangan_tujuan'          => 'required|string|max:255|different:ruangan_asal',
            'penanggung_jawab_asal'   => 'required|string|max:255',
            'penanggung_jawab_tujuan' => 'required|string|max:255',
            'alasan_mutasi'           => 'required|string|max:2000',
            'catatan_penerima'        => 'nullable|string|max:1000',
        ], [
            'ruangan_tujuan.different' => 'Ruangan tujuan harus berbeda dengan ruangan asal.',
        ]);

        $registerIds = $request->input('astap_register_ids', []);
        if (empty($registerIds) && $request->astap_register_id) {
            $registerIds = [$request->astap_register_id];
        }
        if (empty($registerIds)) {
            $registerIds = [$mutasi->astap_register_id];
        }

        $kondisiBaru = $request->input('kondisi_baru', []);

        // 1. Update Dokumen Header Mutasi
        $firstRegId = $registerIds[0] ?? $mutasi->astap_register_id;
        $mutasi->update([
            'astap_register_id'        => $firstRegId,
            'tanggal_mutasi'           => $request->tanggal_mutasi ?: ($mutasi->tanggal_mutasi ?: now()),
            'jenis_mutasi'             => $request->jenis_mutasi,
            'ruangan_asal'             => $request->ruangan_asal,
            'ruangan_tujuan'           => $request->ruangan_tujuan,
            'penanggung_jawab_asal'    => $request->penanggung_jawab_asal,
            'penanggung_jawab_tujuan'  => $request->penanggung_jawab_tujuan,
            'alasan_mutasi'            => $request->alasan_mutasi,
            'catatan_penerima'         => $request->catatan_penerima,
        ]);

        // 2. Sinkronkan Rincian Item Register
        AstapMutasiRegister::where('astap_mutasi_id', $mutasi->id)
            ->whereNotIn('astap_register_id', $registerIds)
            ->delete();

        foreach ($registerIds as $regId) {
            $regObj = AstapRegister::find($regId);
            if (isset($kondisiBaru[$regId]) && in_array($kondisiBaru[$regId], ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'])) {
                if ($regObj && $regObj->kondisi !== $kondisiBaru[$regId]) {
                    $regObj->update(['kondisi' => $kondisiBaru[$regId]]);
                }
            }
            $kondisiSaatMutasi = $regObj ? $regObj->kondisi : 'Baik';

            AstapMutasiRegister::updateOrCreate(
                [
                    'astap_mutasi_id'   => $mutasi->id,
                    'astap_register_id' => $regId,
                ],
                [
                    'kondisi'           => $kondisiSaatMutasi,
                ]
            );
        }

        return redirect()->route('mutasi.index')
            ->with('success', 'Pengajuan Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') berhasil diperbarui.');
    }

    /**
     * Hapus pengajuan mutasi.
     */
    public function destroy(Request $request, $id)
    {
        $mutasi = AstapMutasi::findOrFail($id);
        $bamb = $mutasi->nomor_bamb;
        $mutasi->delete();

        session()->flash('success', "Pengajuan Berita Acara Mutasi {$bamb} berhasil dihapus.");
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('mutasi.index')
            ->with('success', "Pengajuan Berita Acara Mutasi {$bamb} berhasil dihapus.");
    }

    /**
     * Persetujuan oleh pihak penerima.
     */
    public function approvePenerima(Request $request, $id)
    {
        $mutasi = AstapMutasi::findOrFail($id);

        $mutasi->update([
            'persetujuan_penerima'     => true,
            'tgl_persetujuan_penerima' => now(),
            'status'                   => 'Disetujui 2 Pihak (Menunggu Admin)',
        ]);

        session()->flash('success', 'Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') berhasil disetujui oleh penerima.');
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') berhasil disetujui oleh penerima.');
    }

    /**
     * Persetujuan final oleh Admin / Instalasi Pembekalan.
     * Mengupdate lokasi seluruh unit register yang terdaftar dalam Berita Acara ini.
     */
    public function approveAdmin(Request $request, $id)
    {
        $mutasi = AstapMutasi::with(['items.register', 'register'])->findOrFail($id);

        $mutasi->update([
            'persetujuan_admin'     => true,
            'tgl_persetujuan_admin' => now(),
            'status'                => 'Disetujui Admin (Selesai)',
        ]);

        $targetUnit = Unit::where('nama', $mutasi->ruangan_tujuan)->first();
        $updatedRegistersCount = 0;

        // Loop seluruh register di dalam Berita Acara ini
        if ($mutasi->items->count() > 0) {
            foreach ($mutasi->items as $item) {
                if ($item->register) {
                    $updateData = [
                        'ruang_pemegang' => $mutasi->ruangan_tujuan,
                    ];
                    if ($targetUnit) {
                        $updateData['unit_id'] = $targetUnit->id;
                    }
                    if ($item->kondisi) {
                        $updateData['kondisi'] = $item->kondisi;
                    }
                    if ($mutasi->jenis_mutasi === 'Penghapusan') {
                        $updateData['status'] = 'Dihapuskan';
                    }
                    $item->register->update($updateData);
                    $updatedRegistersCount++;
                }
            }
        } elseif ($mutasi->register) {
            // Fallback legacy single register
            $updateData = [
                'ruang_pemegang' => $mutasi->ruangan_tujuan,
            ];
            if ($targetUnit) {
                $updateData['unit_id'] = $targetUnit->id;
            }
            if ($mutasi->jenis_mutasi === 'Penghapusan') {
                $updateData['status'] = 'Dihapuskan';
            }
            $mutasi->register->update($updateData);
            $updatedRegistersCount++;
        }

        $msg = "Berita Acara Mutasi ({$mutasi->nomor_bamb}) telah disahkan oleh Admin! Lokasi {$updatedRegistersCount} aset berhasil dipindahkan ke {$mutasi->ruangan_tujuan}.";
        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', $msg);
    }

    /**
     * Tolak pengajuan mutasi.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        $mutasi = AstapMutasi::findOrFail($id);

        $alasan = $request->input('alasan_penolakan') ?? $request->json('alasan_penolakan');

        $mutasi->update([
            'status'           => 'Ditolak',
            'alasan_penolakan' => $alasan,
        ]);

        session()->flash('success', 'Pengajuan Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') ditolak.');
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Pengajuan Berita Acara Mutasi (' . $mutasi->nomor_bamb . ') ditolak.');
    }

    /**
     * API: Ambil data barang berdasarkan astap_register_id untuk autocomplete form.
     */
    public function getRegisterData($id)
    {
        $register = AstapRegister::with('astap', 'unit')->find($id);

        if (!$register) {
            return response()->json(['success' => false], 404);
        }

        return response()->json([
            'success'        => true,
            'id'             => $register->id,
            'nibar'          => $register->nibar,
            'nama_barang'    => $register->astap->nama_barang ?? '-',
            'kondisi'        => $register->kondisi,
            'ruang_pemegang' => $register->ruang_pemegang,
            'unit_id'        => $register->unit_id,
            'unit_nama'      => $register->unit->nama ?? '-',
            'unit_kepala'    => $register->unit->kepala ?? '-',
        ]);
    }
}

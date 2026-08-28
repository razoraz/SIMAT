<?php

namespace App\Http\Controllers;

use App\Models\AstapMutasi;
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
        $rawMutasis = AstapMutasi::with('register.astap')->latest()->get();
        $mutasis = $rawMutasis->map(function ($m) {
            return [
                'id'                      => $m->id,
                'kode'                    => $m->nomor_bamb,
                'jenis'                   => $m->jenis_mutasi,
                'nama'                    => $m->register?->astap?->nama_barang ?? '(Aset Tanpa Nama)',
                'kode_barang'             => $m->register?->nibar ?? '-',
                'kode_108'                => $m->register?->astap?->kode_108 ?? ($m->register?->kode_108 ?? '-'),
                'kondisi'                 => $m->register?->kondisi ?? 'Baik',
                'asal'                    => $m->ruangan_asal,
                'tujuan'                  => $m->ruangan_tujuan,
                'tgl'                     => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('d M Y') : '-',
                'tgl_raw'                 => $m->tanggal_mutasi ? $m->tanggal_mutasi->format('Y-m-d') : null,
                'pemohon'                 => $m->penanggung_jawab_asal,
                'penerima_pj'             => $m->penanggung_jawab_tujuan,
                'persetujuan_pengirim'    => (bool) $m->persetujuan_pengirim,
                'persetujuan_penerima'    => (bool) $m->persetujuan_penerima,
                'persetujuan_admin'       => (bool) $m->persetujuan_admin,
                'status'                  => $m->status,
                'keterangan'              => $m->alasan_mutasi,
                'catatan_penerima'        => $m->catatan_penerima,
                'alasan_penolakan'        => $m->alasan_penolakan,
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
     */
    public function store(Request $request)
    {
        $request->validate([
            'astap_register_id'       => 'nullable|exists:astap_registers,id',
            'jenis_mutasi'            => 'required|in:Pemindahan,Perbaikan,Pengembalian,Penghapusan',
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

        // Generate nomor BAMB otomatis: MTS-YYYY-NNN
        $year  = date('Y', strtotime($request->tanggal_mutasi));
        $count = AstapMutasi::whereYear('tanggal_mutasi', $year)->count();
        $nomor = 'MTS-' . $year . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        AstapMutasi::create([
            'astap_register_id'        => $request->astap_register_id,
            'nomor_bamb'               => $nomor,
            'tanggal_mutasi'           => $request->tanggal_mutasi,
            'jenis_mutasi'             => $request->jenis_mutasi,
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

        return redirect()->route('mutasi.index')
            ->with('success', 'Pengajuan mutasi ' . $request->jenis_mutasi . ' berhasil dikirim! Menunggu persetujuan penerima.');
    }

    /**
     * Tampilkan form edit mutasi.
     */
    public function edit($id)
    {
        $mutasi    = AstapMutasi::with('register.astap', 'register.unit')->findOrFail($id);
        $units     = Unit::orderBy('nama')->get(['id', 'nama', 'kepala']);
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

        return view('pages.form_mutasi_aset', compact('mutasi', 'units', 'registers'));
    }

    /**
     * Update data mutasi.
     */
    public function update(Request $request, $id)
    {
        $mutasi = AstapMutasi::findOrFail($id);

        $request->validate([
            'astap_register_id'       => 'nullable|exists:astap_registers,id',
            'jenis_mutasi'            => 'required|in:Pemindahan,Perbaikan,Pengembalian,Penghapusan',
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

        $mutasi->update([
            'astap_register_id'       => $request->astap_register_id ?: $mutasi->astap_register_id,
            'jenis_mutasi'            => $request->jenis_mutasi,
            'tanggal_mutasi'          => $request->tanggal_mutasi,
            'ruangan_asal'            => $request->ruangan_asal,
            'ruangan_tujuan'          => $request->ruangan_tujuan,
            'penanggung_jawab_asal'   => $request->penanggung_jawab_asal,
            'penanggung_jawab_tujuan' => $request->penanggung_jawab_tujuan,
            'alasan_mutasi'           => $request->alasan_mutasi,
            'catatan_penerima'        => $request->catatan_penerima,
        ]);

        return redirect()->route('mutasi.index')
            ->with('success', 'Pengajuan mutasi berhasil diperbarui.');
    }

    /**
     * Hapus pengajuan mutasi.
     */
    /**
     * Hapus pengajuan mutasi.
     */
    public function destroy(Request $request, $id)
    {
        $mutasi = AstapMutasi::findOrFail($id);
        $mutasi->delete();

        session()->flash('success', 'Pengajuan mutasi berhasil dihapus.');
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('mutasi.index')
            ->with('success', 'Pengajuan mutasi berhasil dihapus.');
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

        session()->flash('success', 'Mutasi berhasil disetujui oleh penerima.');
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Mutasi berhasil disetujui oleh penerima.');
    }

    /**
     * Persetujuan final oleh Admin / Instalasi Pembekalan.
     */
    public function approveAdmin(Request $request, $id)
    {
        $mutasi = AstapMutasi::with('register')->findOrFail($id);

        $mutasi->update([
            'persetujuan_admin'     => true,
            'tgl_persetujuan_admin' => now(),
            'status'                => 'Disetujui Admin (Selesai)',
        ]);

        // Sinkronkan lokasi aset dan unit pemegang di master register
        if ($mutasi->register) {
            $targetUnit = Unit::where('nama', $mutasi->ruangan_tujuan)->first();
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
        }

        session()->flash('success', 'Mutasi telah disahkan oleh Admin. Lokasi aset berhasil diperbarui ke ' . $mutasi->ruangan_tujuan . '.');
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Mutasi telah disahkan oleh Admin. Lokasi aset berhasil diperbarui ke ' . $mutasi->ruangan_tujuan . '.');
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

        // Support both JSON body (fetch) and form POST
        $alasan = $request->input('alasan_penolakan') ?? $request->json('alasan_penolakan');

        $mutasi->update([
            'status'           => 'Ditolak',
            'alasan_penolakan' => $alasan,
        ]);

        session()->flash('success', 'Pengajuan mutasi ditolak.');
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Pengajuan mutasi ditolak.');
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

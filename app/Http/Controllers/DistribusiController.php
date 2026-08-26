<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\DistribusiItem;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\Unit;
use App\Models\JenisAstap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistribusiController extends Controller
{
    /**
     * Tampilkan Tabel Daftar Distribusi
     */
    public function index()
    {
        $units = Unit::orderBy('id', 'asc')->get()->map(function($u) {
            return [
                'id'      => $u->id,
                'kode'    => $u->kode_unit ?: ('UNIT-' . str_pad($u->id, 3, '0', STR_PAD_LEFT)),
                'nama'    => $u->nama,
                'tipe'    => $u->tipe,
                'kepala'  => $u->kepala,
                'nip'     => $u->nip ?: '-',
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama
            ];
        });

        $distribusis = Distribusi::with(['unit', 'items.astap.jenisAstap'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($d) {
                $itemsMapped = $d->items->map(function($it) {
                    $spec = is_array($it->astap?->spesifikasi_json) 
                        ? $it->astap->spesifikasi_json 
                        : (json_decode($it->astap?->spesifikasi_json ?? '', true) ?? []);
                    $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? '-'));
                    return [
                        'id'          => $it->id,
                        'astap_id'    => $it->astap_id,
                        'nama_barang' => $it->astap?->nama_barang ?? '-',
                        'kode_barang' => $it->astap?->kode_108 ?? '-',
                        'jenis_nama'  => $it->astap?->jenisAstap?->nama_jenis ?? '-',
                        'qty'         => $it->qty,
                        'satuan'      => $it->astap?->satuan ?: 'Unit',
                        'merk'        => $merk,
                        'kondisi'     => $it->kondisi ?: 'Baik',
                        'nibar_list'  => $it->nibar_list ?: [],
                        'keterangan'  => $it->keterangan,
                    ];
                });

                $firstItemName = $itemsMapped->first()['nama_barang'] ?? 'Barang Aset';
                $moreCount = $itemsMapped->count() > 1 ? ' + ' . ($itemsMapped->count() - 1) . ' item lainnya' : '';

                // Tanggal format Indonesia
                $tglCarbon = $d->tanggal_distribusi;
                $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

                $hariStr = $tglCarbon ? ($hariIndo[$tglCarbon->dayOfWeek] ?? 'Kamis') : 'Kamis';
                $tglAngka = $tglCarbon ? (string)$tglCarbon->day : '13';
                $bulanStr = $tglCarbon ? ($bulanIndo[$tglCarbon->month] ?? 'Agustus') : 'Agustus';
                $tahunStr = $tglCarbon ? (string)$tglCarbon->year : '2026';

                return [
                    'id'                 => $d->id,
                    'kode'               => $d->kode,
                    'bast_nomor'         => $d->bast_nomor ?: ($d->kode . '/BAST/2026'),
                    'nomor_bast'         => $d->bast_nomor ?: ($d->kode . '/BAST/2026'),
                    'tgl'                => $tglCarbon ? $tglCarbon->format('d/m/Y') : '-',
                    'tanggal_distribusi' => $tglCarbon ? $tglCarbon->format('Y-m-d') : null,
                    'hari'               => $hariStr,
                    'tanggal_angka'      => $tglAngka,
                    'bulan'              => $bulanStr,
                    'tahun'              => $tahunStr,
                    'tahun_anggaran'     => $tahunStr,
                    'sk_bupati_nomor'    => '188.45/430.10.7/2026',
                    'nama'               => $firstItemName . $moreCount,
                    'unit_id'            => $d->unit_id,
                    'tujuan'             => $d->unit?->nama ?? '-',
                    'penerima'           => $d->unit?->kepala ?? '-',
                    'pj_nama'            => $d->unit?->kepala ?? '-',
                    'pj_nip'             => $d->unit?->nip ?? '-',
                    'pj_ruangan'         => $d->unit?->nama ?? '-',
                    'pj_jabatan'         => 'Kepala / Penanggung Jawab ' . ($d->unit?->nama ?? ''),
                    'status'             => $d->status,
                    'signed'             => (bool)$d->signed,
                    'tgl_signed'         => $d->tgl_signed ?: ($d->signed ? ($d->updated_at ? $d->updated_at->format('d/m/Y H:i') . ' WIB' : '16/06/2026 10:15 WIB') : '-'),
                    'qr_hash'            => $d->signed ? ('BSRE-KOESNANDI-' . $d->kode) : '',
                    'keterangan'         => $d->keterangan,
                    'items'              => $itemsMapped->toArray(),
                ];
            });

        return view('pages.distribusi', compact('distribusis', 'units'));
    }

    /**
     * Form Input Distribusi Baru
     */
    public function create()
    {
        $units = Unit::orderBy('id', 'asc')->get()->map(function($u) {
            return [
                'id'      => $u->id,
                'nama'    => $u->nama,
                'tipe'    => $u->tipe,
                'kepala'  => $u->kepala,
                'nip'     => $u->nip ?: '-',
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama
            ];
        });

        $jenisAstapList = JenisAstap::whereNotNull('nama_jenis')
            ->where('nama_jenis', '!=', '')
            ->where('nama_jenis', '!=', '-')
            ->select('jenis', 'nama_jenis')
            ->distinct()
            ->orderBy('jenis')
            ->get()
            ->filter(fn($j) => !empty(trim($j->nama_jenis ?? '')))
            ->map(function($j) {
                return [
                    'kode' => $j->jenis,
                    'nama' => trim($j->nama_jenis),
                ];
            })
            ->unique('nama')
            ->values();

        $astapList = Astap::with('jenisAstap')
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function($a) {
                $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
                $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? ''));
                $jenisKode = $a->jenisAstap ? $a->jenisAstap->jenis : substr($a->kode_108, 0, 5);
                $jenisNama = $a->jenisAstap ? $a->jenisAstap->nama_jenis : '';
                return [
                    'id'         => $a->id,
                    'kode'       => $a->kode_108,
                    'nama'       => $a->nama_barang,
                    'jenis_kode' => $jenisKode,
                    'jenis_nama' => $jenisNama,
                    'kategori'   => $a->category,
                    'merk'       => $merk,
                    'satuan'     => $a->satuan ?: 'Unit',
                ];
            });

        $nibarList = AstapRegister::with('astap')
            ->orderBy('astap_id')
            ->orderBy('no_register_int')
            ->get()
            ->map(function($r) {
                return [
                    'id'          => $r->id,
                    'astap_id'    => $r->astap_id,
                    'nibar'       => $r->nibar ?: $r->no_register,
                    'kode'        => $r->astap ? $r->astap->kode_108 : '',
                    'nama_barang' => $r->astap ? $r->astap->nama_barang : '',
                    'ruang'       => $r->ruang_pemegang ?: 'Belum Ditempatkan / Di Gudang',
                    'kondisi'     => $r->kondisi ?: 'Baik',
                    'status'      => $r->status_mutasi ?: 'Tersedia',
                ];
            });

        return view('pages.form_distribusi', compact('units', 'jenisAstapList', 'astapList', 'nibarList'));
    }

    /**
     * Form Ubah Distribusi
     */
    public function edit($id)
    {
        $units = Unit::orderBy('id', 'asc')->get()->map(function($u) {
            return [
                'id'      => $u->id,
                'nama'    => $u->nama,
                'tipe'    => $u->tipe,
                'kepala'  => $u->kepala,
                'nip'     => $u->nip ?: '-',
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama
            ];
        });

        $jenisAstapList = JenisAstap::whereNotNull('nama_jenis')
            ->where('nama_jenis', '!=', '')
            ->where('nama_jenis', '!=', '-')
            ->select('jenis', 'nama_jenis')
            ->distinct()
            ->orderBy('jenis')
            ->get()
            ->filter(fn($j) => !empty(trim($j->nama_jenis ?? '')))
            ->map(function($j) {
                return [
                    'kode' => $j->jenis,
                    'nama' => trim($j->nama_jenis),
                ];
            })
            ->unique('nama')
            ->values();

        $astapList = Astap::with('jenisAstap')
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function($a) {
                $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
                $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? ''));
                $jenisKode = $a->jenisAstap ? $a->jenisAstap->jenis : substr($a->kode_108, 0, 5);
                $jenisNama = $a->jenisAstap ? $a->jenisAstap->nama_jenis : '';
                return [
                    'id'         => $a->id,
                    'kode'       => $a->kode_108,
                    'nama'       => $a->nama_barang,
                    'jenis_kode' => $jenisKode,
                    'jenis_nama' => $jenisNama,
                    'kategori'   => $a->category,
                    'merk'       => $merk,
                    'satuan'     => $a->satuan ?: 'Unit',
                ];
            });

        $nibarList = AstapRegister::with('astap')
            ->orderBy('astap_id')
            ->orderBy('no_register_int')
            ->get()
            ->map(function($r) {
                return [
                    'id'          => $r->id,
                    'astap_id'    => $r->astap_id,
                    'nibar'       => $r->nibar ?: $r->no_register,
                    'kode'        => $r->astap ? $r->astap->kode_108 : '',
                    'nama_barang' => $r->astap ? $r->astap->nama_barang : '',
                    'ruang'       => $r->ruang_pemegang ?: 'Belum Ditempatkan / Di Gudang',
                    'kondisi'     => $r->kondisi ?: 'Baik',
                    'status'      => $r->status_mutasi ?: 'Tersedia',
                ];
            });

        $distribusiData = Distribusi::with(['unit', 'items.astap.jenisAstap'])->find($id);

        return view('pages.form_distribusi', compact('units', 'jenisAstapList', 'astapList', 'nibarList', 'id', 'distribusiData'));
    }

    /**
     * Simpan / Perbarui Transaksi Distribusi & Otomatis Update Tabel astap_registers
     */
    public function saveDistribusi(Request $request)
    {
        $validated = $request->validate([
            'kode'               => 'required|string|max:50',
            'bast_nomor'         => 'nullable|string|max:100',
            'tanggal_distribusi' => 'required|date',
            'unit_id'            => 'required|exists:units,id',
            'status'             => 'required|in:Telah Diterima,Dalam Pengiriman,Menunggu Konfirmasi,Draft',
            'keterangan'         => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.astap_id'   => 'required|exists:astaps,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.kondisi'    => 'nullable|string',
            'items.*.nibar_list' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $unit = Unit::findOrFail($validated['unit_id']);

            // 1. Simpan / Update Header Distribusi
            $distribusi = Distribusi::updateOrCreate(
                ['kode' => $validated['kode']],
                [
                    'bast_nomor'         => $validated['bast_nomor'],
                    'tanggal_distribusi' => $validated['tanggal_distribusi'],
                    'unit_id'            => $validated['unit_id'],
                    'status'             => $validated['status'],
                    'keterangan'         => $validated['keterangan'] ?? null,
                ]
            );

            // 2. Simpan Item Rincian Distribusi
            $distribusi->items()->delete();

            foreach ($validated['items'] as $itemData) {
                $nibarList = $itemData['nibar_list'] ?? [];
                $kondisi = in_array($itemData['kondisi'] ?? 'Baik', ['Baik', 'Kurang Baik', 'Rusak Berat']) 
                    ? ($itemData['kondisi'] ?? 'Baik') 
                    : 'Baik';

                $distribusiItem = DistribusiItem::create([
                    'distribusi_id' => $distribusi->id,
                    'astap_id'      => $itemData['astap_id'],
                    'qty'           => $itemData['qty'],
                    'nibar_list'    => $nibarList,
                    'kondisi'       => $kondisi,
                    'keterangan'    => $itemData['keterangan'] ?? null,
                ]);

                // 3. UPDATE OTOMATIS TABEL astap_registers UNTUK SETIAP NIBAR TERKAIT
                if (!empty($nibarList)) {
                    AstapRegister::whereIn('nibar', $nibarList)->update([
                        'unit_id'        => $unit->id,             // Update Unit ID Ruangan Tujuan
                        'ruang_pemegang' => $unit->nama,           // Update Nama Ruang Pemegang
                        'kondisi'        => $kondisi,              // Update Kondisi Fisik Terkini
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi distribusi berhasil disimpan dan database astap_registers telah diperbarui.',
                'data'    => $distribusi->load('items.astap', 'unit')
            ]);
        });
    }
}

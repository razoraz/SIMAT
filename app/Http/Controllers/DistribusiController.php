<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\DistribusiItem;
use App\Models\DistribusiItemRegister;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\Unit;
use App\Models\JenisAstap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistribusiController extends Controller
{
    /**
     * Generate Nomor Urut BAST Resmi Sekuensial per Tahun
     * Hanya dihitung dari transaksi yang berstatus 'Dalam Pengiriman' atau 'Telah Diterima'
     */
    public static function generateNextBastNomor(int $tahun, ?int $excludeId = null): string
    {
        $existingBastNumbers = Distribusi::whereYear('tanggal_distribusi', $tahun)
            ->whereIn('status', ['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima'])
            ->whereNotNull('bast_nomor')
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->pluck('bast_nomor');

        $maxSeq = 0;
        foreach ($existingBastNumbers as $no) {
            if (preg_match('/032\s*\/\s*(\d+)\s*\/\s*430\.10\.7/', (string)$no, $m)) {
                $seqVal = (int) $m[1];
                if ($seqVal > $maxSeq) {
                    $maxSeq = $seqVal;
                }
            }
        }

        $nextSeq = $maxSeq + 1;
        return '032 / ' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT) . ' / 430.10.7 / ' . $tahun;
    }

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
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama,
            ];
        });

        $user = Auth::user();
        $isSubAdmin = $user && $user->isSubAdmin();

        // Eager load: registers → astapRegister agar kondisi, nibar, ruang dibaca dari FK (tidak query N+1)
        $distribusiQuery = Distribusi::with([
                'unit',
                'items.astap.jenisAstap',
                'items.registers.astapRegister',
            ]);

        // Jika sub admin, batasi data hanya untuk unit miliknya
        if ($isSubAdmin && $user->unit_id) {
            $distribusiQuery->where('unit_id', $user->unit_id);
        }

        $unitPerbekalan = Unit::where('nama', 'LIKE', '%perbekalan%')
            ->orWhere('nama', 'LIKE', '%rumah tangga%')
            ->first();

        $distribusis = $distribusiQuery->orderBy('id', 'desc')
            ->get()
            ->map(function($d) use ($unitPerbekalan) {
                $itemsMapped = $d->items->map(function($it) {
                    $spec = is_array($it->astap?->spesifikasi_json)
                        ? $it->astap->spesifikasi_json
                        : (json_decode($it->astap?->spesifikasi_json ?? '', true) ?? []);
                    $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? '-'));

                    // Baca register NIBAR melalui relasi FK (bukan whereIn string)
                    $nibarRegisters = $it->registers->map(function($dir) {
                        $reg = $dir->astapRegister;
                        return [
                            'nibar'   => $reg?->nibar   ?? '-',
                            'reg_id'  => $reg?->id      ?? null,
                            'kondisi' => $reg?->kondisi ?? 'Baik',
                            'ruang'   => $reg?->ruang_pemegang ?? 'Gudang Aset',
                        ];
                    })->values()->all();

                    $nibarList = collect($nibarRegisters)->pluck('nibar')->filter()->values()->all();

                    // Kondisi dominan dari register pertama (default '-' jika belum ada NIBAR)
                    $firstKondisi = $nibarRegisters[0]['kondisi'] ?? '-';

                    return [
                        'id'              => $it->id,
                        'astap_id'        => $it->astap_id,
                        'nama_barang'     => $it->astap?->nama_barang ?? '-',
                        'kode_barang'     => $it->astap?->kode_108 ?? '-',
                        'jenis_nama'      => $it->astap?->jenisAstap?->nama_jenis ?? '-',
                        'qty'             => $it->qty,
                        'qty_acc'         => $it->qty_acc !== null ? (int)$it->qty_acc : null,
                        'vol_bast'        => $it->qty_acc !== null ? (int)$it->qty_acc : '-',
                        'satuan'          => $it->astap?->satuan ?: 'Unit',
                        'merk'            => $merk,
                        'merk_type'       => $merk,
                        'kondisi'         => $firstKondisi,
                        'nibar_list'      => $nibarList,           // Untuk kompatibilitas tampilan
                        'nibar_registers' => $nibarRegisters,       // Detail per NIBAR dari FK
                        'keterangan'      => $it->keterangan,
                    ];
                });

                $firstItemName = $itemsMapped->first()['nama_barang'] ?? 'Barang Aset';
                $moreCount = $itemsMapped->count() > 1 ? ' + ' . ($itemsMapped->count() - 1) . ' item lainnya' : '';

                $tglCarbon = $d->tanggal_distribusi;
                $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $hariIndo  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

                $hariStr  = $tglCarbon ? ($hariIndo[$tglCarbon->dayOfWeek] ?? 'Kamis') : 'Kamis';
                $tglAngka = $tglCarbon ? (string)$tglCarbon->day : '13';
                $bulanStr = $tglCarbon ? ($bulanIndo[$tglCarbon->month] ?? 'Agustus') : 'Agustus';
                $tahunStr = $tglCarbon ? (string)$tglCarbon->year : '2026';

                $isDeliveredOrReceived = in_array($d->status, ['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima']);
                $isValidBast = !empty($d->bast_nomor) && preg_match('/032\s*\/\s*\d+\s*\/\s*430\.10\.7/', (string)$d->bast_nomor);

                if ($d->status === 'Ditolak') {
                    $bastNomorTampil = '(tidak diterbitkan)';
                } elseif ($isDeliveredOrReceived && $isValidBast) {
                    $bastNomorTampil = $d->bast_nomor;
                } elseif ($isDeliveredOrReceived) {
                    $bastNomorTampil = self::generateNextBastNomor((int)$tahunStr, $d->id);
                } else {
                    // Menunggu Konfirmasi atau Draft
                    $bastNomorTampil = '(Menunggu Konfirmasi)';
                }

                return [
                    'id'                 => $d->id,
                    'kode'               => $d->kode,
                    'bast_nomor'         => $bastNomorTampil,
                    'nomor_bast'         => $bastNomorTampil,
                    'tgl'                => $tglCarbon ? $tglCarbon->format('d/m/Y') : '-',
                    'tanggal_distribusi' => $tglCarbon ? $tglCarbon->format('Y-m-d') : null,
                    'hari'               => $hariStr,
                    'tanggal_angka'      => $tglAngka,
                    'bulan'              => $bulanStr,
                    'tahun'              => $tahunStr,
                    'tahun_anggaran'     => $tahunStr,
                    'sk_bupati_nomor'    => '188.45/430.10.7/2026',
                    'sk_bupati_tanggal'  => '02 Januari ' . $tahunStr,
                    'nama'               => $firstItemName . $moreCount,
                    'unit_id'            => $d->unit_id,
                    'tujuan'             => $d->unit?->nama ?? '-',
                    'penerima'           => $d->unit?->kepala ?? '-',
                    'pj_nama'            => $d->unit?->kepala ?? '-',
                    'pj_nip'             => $d->unit?->nip ?? '-',
                    'pj_ruangan'         => $d->unit?->nama ?? '-',
                    'pj_jabatan'         => 'Kepala / Penanggung Jawab ' . ($d->unit?->nama ?? ''),
                    'pengurus_nama'      => $unitPerbekalan?->kepala ?: 'BUDI HARTONO, S. Sos',
                    'pengurus_nip'       => $unitPerbekalan?->nip ?: '197602292008011010',
                    'pengurus_jabatan'   => 'Pengurus Barang Aset',
                    'pengurus_ruangan'   => $unitPerbekalan?->nama ?: 'Bagian Rumah Tangga & Inst Perbekalan',
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
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama,
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
                return ['kode' => $j->jenis, 'nama' => trim($j->nama_jenis)];
            })
            ->unique('nama')
            ->values();

        $astapList = Astap::with('jenisAstap')
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function($a) {
                $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
                $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? ''));
                return [
                    'id'         => $a->id,
                    'kode'       => $a->kode_108,
                    'nama'       => $a->nama_barang,
                    'jenis_kode' => $a->jenisAstap ? $a->jenisAstap->jenis : substr($a->kode_108, 0, 5),
                    'jenis_nama' => $a->jenisAstap ? $a->jenisAstap->nama_jenis : '',
                    'kategori'   => $a->category,
                    'merk'       => $merk,
                    'satuan'     => $a->satuan ?: 'Unit',
                ];
            });

        // Kirim register list dengan id (bukan nibar string) sebagai referensi FK
        $nibarList = AstapRegister::with('astap')
            ->orderBy('astap_id')
            ->orderBy('no_register_int')
            ->get()
            ->map(function($r) {
                $isTersedia = empty($r->ruang_pemegang) || $r->status === 'Tersedia';
                return [
                    'id'          => $r->id,            // ← FK integer yang akan disimpan
                    'astap_id'    => $r->astap_id,
                    'nibar'       => $r->nibar ?: $r->no_register,
                    'kode'        => $r->astap ? $r->astap->kode_108 : '',
                    'nama_barang' => $r->astap ? $r->astap->nama_barang : '',
                    'ruang'       => $r->ruang_pemegang ?: 'Belum Ditempatkan / Di Gudang',
                'kondisi'     => $r->kondisi ?: 'Baik',
                    'status'      => $isTersedia ? 'Tersedia' : 'Tidak Tersedia',
                ];
            });

        // Hitung kode urut berikutnya agar tampil di form (bukan random)
        $tahunIni = date('Y');
        $nextSeq = Distribusi::whereYear('tanggal_distribusi', $tahunIni)->count() + 1;
        $nextKode = 'DST-' . $tahunIni . '-' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
        $nextBastNomor = self::generateNextBastNomor((int)$tahunIni);

        return view('pages.form_distribusi', compact('units', 'jenisAstapList', 'astapList', 'nibarList', 'nextKode', 'nextBastNomor'));
    }

    /**
     * Form Ubah Distribusi
     */
    public function edit($id)
    {
        $distribusiData = Distribusi::with([
            'unit',
            'items.astap.jenisAstap',
            'items.registers.astapRegister',
        ])->find($id);

        // Ambil register_id yang sudah dipakai di distribusi ini agar status Tersedia saat edit
        $currentRegisterIds = [];
        if ($distribusiData && $distribusiData->items) {
            foreach ($distribusiData->items as $it) {
                $currentRegisterIds = array_merge(
                    $currentRegisterIds,
                    $it->registers->pluck('astap_register_id')->toArray()
                );
            }
        }

        $units = Unit::orderBy('id', 'asc')->get()->map(function($u) {
            return [
                'id'      => $u->id,
                'nama'    => $u->nama,
                'tipe'    => $u->tipe,
                'kepala'  => $u->kepala,
                'nip'     => $u->nip ?: '-',
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama,
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
                return ['kode' => $j->jenis, 'nama' => trim($j->nama_jenis)];
            })
            ->unique('nama')
            ->values();

        $astapList = Astap::with('jenisAstap')
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function($a) {
                $spec = is_array($a->spesifikasi_json) ? $a->spesifikasi_json : (json_decode($a->spesifikasi_json, true) ?? []);
                $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? ''));
                return [
                    'id'         => $a->id,
                    'kode'       => $a->kode_108,
                    'nama'       => $a->nama_barang,
                    'jenis_kode' => $a->jenisAstap ? $a->jenisAstap->jenis : substr($a->kode_108, 0, 5),
                    'jenis_nama' => $a->jenisAstap ? $a->jenisAstap->nama_jenis : '',
                    'kategori'   => $a->category,
                    'merk'       => $merk,
                    'satuan'     => $a->satuan ?: 'Unit',
                ];
            });

        $nibarList = AstapRegister::with('astap')
            ->orderBy('astap_id')
            ->orderBy('no_register_int')
            ->get()
            ->map(function($r) use ($currentRegisterIds) {
                $isOwn      = in_array($r->id, $currentRegisterIds);
                $isTersedia = empty($r->ruang_pemegang) || $r->status === 'Tersedia' || $isOwn;
                return [
                    'id'          => $r->id,
                    'astap_id'    => $r->astap_id,
                    'nibar'       => $r->nibar ?: $r->no_register,
                    'kode'        => $r->astap ? $r->astap->kode_108 : '',
                    'nama_barang' => $r->astap ? $r->astap->nama_barang : '',
                    'ruang'       => $r->ruang_pemegang ?: 'Belum Ditempatkan / Di Gudang',
                    'kondisi'     => $r->kondisi ?: 'Baik',
                    'status'      => $isTersedia ? 'Tersedia' : 'Tidak Tersedia',
                ];
            });

        // Proteksi: sub admin tidak bisa mengubah pengajuan distribusi
        $user = auth()->user();
        if ($user && $user->isSubAdmin()) {
            abort(403, 'Sub Admin tidak memiliki akses untuk mengubah data pengajuan distribusi.');
        }

        $tahunIni = $distribusiData?->tanggal_distribusi ? date('Y', strtotime($distribusiData->tanggal_distribusi)) : date('Y');
        $nextBastNomor = self::generateNextBastNomor((int)$tahunIni, $distribusiData?->id);

        return view('pages.form_distribusi', compact('units', 'jenisAstapList', 'astapList', 'nibarList', 'id', 'distribusiData', 'nextBastNomor'));
    }

    /**
     * Simpan / Perbarui Transaksi Distribusi
     */
    public function saveDistribusi(Request $request, $id = null)
    {
        $user = auth()->user();
        $isSubAdmin = $user && $user->isSubAdmin();

        // 1. Resolve unit_id jika belum terisi atau terkirim dalam bentuk nama/tujuan
        if (empty($request->unit_id) && !empty($request->tujuan)) {
            $unitFound = Unit::where('nama', 'LIKE', '%' . trim($request->tujuan) . '%')->first();
            if ($unitFound) {
                $request->merge(['unit_id' => $unitFound->id]);
            }
        }
        if (empty($request->unit_id)) {
            $firstUnit = Unit::first();
            if ($firstUnit) {
                $request->merge(['unit_id' => $firstUnit->id]);
            }
        }

        // 2. Validasi Input Distribusi
        $validated = $request->validate([
            'kode'                         => 'required|string',
            'bast_nomor'                   => 'nullable|string',
            'tanggal_distribusi'           => 'nullable|date',
            'unit_id'                      => 'required|integer|exists:units,id',
            'status'                       => 'nullable|string',
            'keterangan'                   => 'nullable|string',
            'items'                        => 'required|array|min:1',
            'items.*.astap_id'             => 'nullable',
            'items.*.qty'                  => 'nullable|integer|min:1',
            'items.*.qty_acc'              => 'nullable|integer|min:0',
            'items.*.keterangan'           => 'nullable|string',
            'items.*.register_ids'         => 'nullable|array',
        ]);

        return DB::transaction(function () use ($validated, $request, $user, $isSubAdmin, $id) {
            $finalUnitId = ($isSubAdmin && $user->unit_id) ? $user->unit_id : $validated['unit_id'];
            $unit = Unit::findOrFail($finalUnitId);

            $tglDistribusi = !empty($validated['tanggal_distribusi'])
                ? date('Y-m-d', strtotime($validated['tanggal_distribusi']))
                : date('Y-m-d');
            $tahunDistribusi = date('Y', strtotime($tglDistribusi));

            // Status sesuai role & input:
            // - Sub admin buat baru / perbarui → 'Menunggu Konfirmasi'
            // - Admin / master admin → gunakan status yang dipilih di form, atau default
            $allowedStatuses = ['Menunggu Konfirmasi', 'Dalam Pengiriman', 'Telah Diterima', 'Ditolak'];
            if ($isSubAdmin) {
                $finalStatus = 'Menunggu Konfirmasi';
            } elseif (!empty($validated['status']) && in_array($validated['status'], $allowedStatuses)) {
                $finalStatus = $validated['status'];
            } else {
                $finalStatus = !$id ? 'Menunggu Konfirmasi' : 'Dalam Pengiriman';
            }

            // Helper: hitung nomor urut sekuensial kode transaksi per tahun
            $nextSeq = Distribusi::whereYear('tanggal_distribusi', $tahunDistribusi)->count() + 1;

            $getSeqForExisting = function (Distribusi $d) use ($tahunDistribusi): int {
                return Distribusi::whereYear('tanggal_distribusi', $tahunDistribusi)
                    ->where('id', '<=', $d->id)
                    ->count();
            };

            $generateKode = function (int $seq) use ($tahunDistribusi): string {
                return 'DST-' . $tahunDistribusi . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
            };

            // 1. Simpan / Update Header Distribusi
            $distribusi = null;
            if ($id) {
                $distribusi = Distribusi::find($id);
            }
            if (!$distribusi) {
                $distribusi = Distribusi::where('kode', $validated['kode'])->first();
            }

            $isNewRecord = !$distribusi;

            // Penentuan Nomor BAST:
            // - Diterbitkan HANYA ketika status 'Dalam Pengiriman' atau 'Telah Diterima'
            // - Nomor urutnya bergantung HANYA pada data yang statusnya 'Dalam Pengiriman' / 'Telah Diterima'
            // - Jika status 'Ditolak', 'Menunggu Konfirmasi', atau 'Draft' -> otomatis TIDAK memiliki nomor BAST (null)
            $isShippingOrReceived = in_array($finalStatus, ['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima']);

            $finalBastNomor = null;
            if ($isShippingOrReceived) {
                $existingBast = $distribusi?->bast_nomor;
                $isValidExistingBast = !empty($existingBast) 
                    && preg_match('/^032\s*\/\s*\d+\s*\/\s*430\.10\.7\s*\/\s*\d{4}$/', trim($existingBast));

                if ($isValidExistingBast) {
                    $finalBastNomor = trim($existingBast);
                } else {
                    $finalBastNomor = self::generateNextBastNomor((int)$tahunDistribusi, $distribusi?->id);
                }
            } else {
                // Status Ditolak, Menunggu Konfirmasi, atau Draft: otomatis TIDAK memiliki nomor BAST
                $finalBastNomor = null;
            }

            if ($distribusi) {
                $seq = $getSeqForExisting($distribusi);
                $updateData = [
                    'kode'               => $generateKode($seq),
                    'bast_nomor'         => $finalBastNomor,
                    'tanggal_distribusi' => $tglDistribusi,
                    'unit_id'            => $finalUnitId,
                    'status'             => $finalStatus,
                    'keterangan'         => $validated['keterangan'] ?? null,
                ];
                if ($finalStatus === 'Ditolak') {
                    $updateData['signed'] = false;
                    $updateData['tgl_signed'] = null;
                } elseif ($finalStatus === 'Telah Diterima') {
                    $updateData['signed'] = true;
                    if (!$distribusi->tgl_signed) {
                        $updateData['tgl_signed'] = now()->format('d/m/Y H:i') . ' WIB';
                    }
                }
                $distribusi->update($updateData);

                // Reset register lama jika ada
                foreach ($distribusi->items as $oldItem) {
                    $oldRegIds = $oldItem->registers->pluck('astap_register_id')->filter()->toArray();
                    if (!empty($oldRegIds)) {
                        AstapRegister::whereIn('id', $oldRegIds)->update([
                            'unit_id'        => null,
                            'ruang_pemegang' => null,
                            'status'         => 'Tersedia',
                        ]);
                    }
                }

                // Hapus item lama
                $distribusi->items()->delete();
            } else {
                // Buat record baru
                $createData = [
                    'kode'               => $generateKode($nextSeq),
                    'bast_nomor'         => $finalBastNomor,
                    'tanggal_distribusi' => $tglDistribusi,
                    'unit_id'            => $finalUnitId,
                    'status'             => $finalStatus,
                    'keterangan'         => $validated['keterangan'] ?? null,
                ];
                if ($finalStatus === 'Ditolak') {
                    $createData['signed'] = false;
                    $createData['tgl_signed'] = null;
                } elseif ($finalStatus === 'Telah Diterima') {
                    $createData['signed'] = true;
                    $createData['tgl_signed'] = now()->format('d/m/Y H:i') . ' WIB';
                }
                $distribusi = Distribusi::create($createData);
            }

            // 2. Simpan Multi-Items & Register NIBAR
            foreach ($validated['items'] as $itemData) {
                $astapId = $itemData['astap_id'] ?? null;

                if (!$astapId || !Astap::where('id', $astapId)->exists()) {
                    $kodeBarang = $itemData['kode_barang'] ?? '';
                    $namaBarang = $itemData['nama_barang'] ?? '';
                    $astapMatch = Astap::where('kode_108', $kodeBarang)
                        ->orWhere('nama_barang', $namaBarang)
                        ->first();

                    if ($astapMatch) {
                        $astapId = $astapMatch->id;
                    } else {
                        $fallbackAstap = Astap::first();
                        $astapId = $fallbackAstap ? $fallbackAstap->id : 1;
                    }
                }

                $distribusiItem = DistribusiItem::create([
                    'distribusi_id' => $distribusi->id,
                    'astap_id'      => $astapId,
                    'qty'           => $itemData['qty'] ?? 1,
                    'qty_acc'       => ($finalStatus === 'Ditolak')
                        ? 0
                        : ((isset($itemData['qty_acc']) && $itemData['qty_acc'] !== null && $itemData['qty_acc'] !== '')
                            ? (int)$itemData['qty_acc']
                            : null),
                    'keterangan'    => $itemData['keterangan'] ?? null,
                ]);

                // Simpan Relasi Register NIBAR terpilih & kunci statusnya (kosongkan jika Ditolak)
                $registerIds = ($finalStatus === 'Ditolak') ? [] : ($itemData['register_ids'] ?? []);

                foreach ($registerIds as $regId) {
                    DistribusiItemRegister::create([
                        'distribusi_item_id' => $distribusiItem->id,
                        'astap_register_id'  => $regId,
                    ]);
                }

                if (!empty($registerIds)) {
                    if ($finalStatus === 'Ditolak') {
                        AstapRegister::whereIn('id', $registerIds)->update([
                            'unit_id'        => null,
                            'ruang_pemegang' => null,
                            'status'         => 'Tersedia',
                        ]);
                    } else {
                        AstapRegister::whereIn('id', $registerIds)->update([
                            'unit_id'        => $unit->id,
                            'ruang_pemegang' => $unit->nama,
                            'status'         => 'Tidak Tersedia',
                        ]);
                    }
                }
            }

            // Kirim Notifikasi Sistem Sesuai Role (Format Singkat & Rapi)
            try {
                if ($isNewRecord) {
                    \App\Services\NotificationService::sendToAdminAndMaster(
                        "Distribusi: {$unit->nama}",
                        "{$distribusi->kode} • Status: {$finalStatus}",
                        'distribusi',
                        route('distribusi.index')
                    );
                    \App\Services\NotificationService::sendToUnitSubAdmin(
                        $unit->id,
                        $unit->nama,
                        "Distribusi Masuk: {$unit->nama}",
                        "{$distribusi->kode} • Status: {$finalStatus}",
                        'distribusi',
                        route('distribusi.index')
                    );
                } else {
                    \App\Services\NotificationService::sendToUnitSubAdmin(
                        $unit->id,
                        $unit->nama,
                        "Distribusi ({$distribusi->kode}) Diperbarui",
                        "Status: {$finalStatus} • {$unit->nama}",
                        'distribusi',
                        route('distribusi.index')
                    );
                }
            } catch (\Throwable $e) {
                \Log::warning("Gagal mengirim notifikasi distribusi: " . $e->getMessage());
            }

            return response()->json([
                'success'    => true,
                'message'    => "Transaksi distribusi {$distribusi->kode} berhasil disimpan.",
                'bast_nomor' => $distribusi->bast_nomor,
                'data'       => $distribusi->load('items.registers.astapRegister', 'unit'),
            ]);
        });
    }

    /**
     * Hapus Transaksi Distribusi
     */
    public function destroy($id)
    {
        $distribusi = Distribusi::findOrFail($id);

        // Proteksi: sub admin tidak bisa menghapus pengajuan distribusi
        $user = auth()->user();
        if ($user && $user->isSubAdmin()) {
            return response()->json(['success' => false, 'message' => 'Sub Admin tidak memiliki izin untuk menghapus data pengajuan distribusi.'], 403);
        }

        $kode = $distribusi->kode;
        DB::transaction(function () use ($distribusi) {
            $distribusi->delete();
        });

        session()->flash('success', "Transaksi Distribusi {$kode} berhasil dihapus.");
        return response()->json(['success' => true, 'message' => "Transaksi Distribusi {$kode} berhasil dihapus."]);
    }
}



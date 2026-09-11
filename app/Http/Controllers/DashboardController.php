<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\JenisAstap;
use App\Models\Unit;
use App\Models\Distribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    /**
     * Redirect role-based dashboard
     */
    public function index()
    {
        $role = Auth::user()->role;
        return match ($role) {
            'master_admin' => redirect()->route('masteradmin.dashboard'),
            'admin'        => redirect()->route('admin.dashboard'),
            'sub_admin'    => redirect()->route('subadmin.dashboard'),
            default        => redirect()->route('login'),
        };
    }

    /**
     * Dashboard Master Admin
     */
    public function masterAdmin()
    {
        $data = $this->getDashboardData();
        return view('dashboards.master_admin', $data);
    }

    /**
     * Dashboard Admin Operasional
     */
    public function admin()
    {
        $data = $this->getDashboardData();
        return view('dashboards.admin', $data);
    }

    /**
     * Helper untuk menghitung data statistik & data tabel ASTAP riil dari DB SQLite
     */
    private function getDashboardData(): array
    {
        // 1. Card 1: Total Keseluruhan Volume Aset ASTAP & Master ASTAP
        $totalAsetVolumeCount   = (int) Astap::sum('jumlah_volume');
        $totalAsetRegisterCount = AstapRegister::count();
        $totalAstapMasterCount  = Astap::count();

        // 2. Card 2: Harga Aset Keseluruhan (Total Rupiah Dana Aset RSUD)
        $totalValuasi = (float) Astap::sum('total_realisasi');
        if ($totalValuasi >= 1000000000) {
            $hargaAsetFormatted = 'Rp ' . number_format($totalValuasi / 1000000000, 2, ',', '.');
            $hargaAsetUnit = 'Miliar';
        } elseif ($totalValuasi >= 1000000) {
            $hargaAsetFormatted = 'Rp ' . number_format($totalValuasi / 1000000, 2, ',', '.');
            $hargaAsetUnit = 'Juta';
        } else {
            $hargaAsetFormatted = 'Rp ' . number_format($totalValuasi, 0, ',', '.');
            $hargaAsetUnit = '';
        }
        $hargaAsetFull = 'Rp ' . number_format($totalValuasi, 0, ',', '.');

        // 3. Card 3: Jumlah Unit RSUD & Distribusi Barang
        $totalUnitRsudCount       = Unit::count();
        $totalTerdistribusiUnit   = AstapRegister::whereNotNull('unit_id')->count();
        $totalTransaksiDistribusi = Distribusi::count();

        // 4. Card 4: Kondisi Aset
        $kondisiBaik         = AstapRegister::where('kondisi', 'Baik')->count();
        $kondisiKurangBaik   = AstapRegister::where('kondisi', 'Kurang Baik')->count();
        $kondisiRusakRingan  = AstapRegister::where('kondisi', 'Rusak Ringan')->count();
        $kondisiRusakBerat   = AstapRegister::whereIn('kondisi', ['Rusak Berat', 'Rusak'])->count();
        $totalRusak          = $kondisiKurangBaik + $kondisiRusakRingan + $kondisiRusakBerat;

        // 5. Data Grafik Peningkatan Aset (Harga & Kuantitas per Tahun)
        $yearlyStats = Astap::selectRaw('tahun_perolehan, SUM(jumlah_volume) as total_volume, SUM(total_realisasi) as total_harga')
            ->whereNotNull('tahun_perolehan')
            ->where('tahun_perolehan', '>', 0)
            ->groupBy('tahun_perolehan')
            ->orderBy('tahun_perolehan', 'asc')
            ->get();

        $chartLabels = [];
        $chartVolumeData = [];
        $chartHargaDataJuta = [];
        $chartKumulatifVolume = [];
        $chartKumulatifHargaJuta = [];

        $runningVolume = 0;
        $runningHarga = 0;

        foreach ($yearlyStats as $stat) {
            $year = (string) $stat->tahun_perolehan;
            $vol = (int) $stat->total_volume;
            $harga = (float) $stat->total_harga;
            $hargaJuta = round($harga / 1000000, 2);

            $runningVolume += $vol;
            $runningHarga += $harga;
            $runningHargaJuta = round($runningHarga / 1000000, 2);

            $chartLabels[] = 'Thn ' . $year;
            $chartVolumeData[] = $vol;
            $chartHargaDataJuta[] = $hargaJuta;
            $chartKumulatifVolume[] = $runningVolume;
            $chartKumulatifHargaJuta[] = $runningHargaJuta;
        }

        // 6. Data ASTAP Terbaru dari SQLite DB
        $recentAstaps = Astap::with(['jenisAstap', 'registers.unit'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($a) {
                $firstReg = $a->registers ? $a->registers->first() : null;
                
                // Cari lokasi unit dari registers
                $lokasiUnits = $a->registers ? $a->registers->map(function($r) {
                    return $r->unit?->nama ?: ($r->ruang_pemegang ?: null);
                })->filter()->unique()->values()->all() : [];

                $lokasiStr = !empty($lokasiUnits) 
                    ? implode(', ', array_slice($lokasiUnits, 0, 2)) 
                    : 'Gudang Aset';

                if (count($lokasiUnits) > 2) {
                    $lokasiStr .= ' (+' . (count($lokasiUnits) - 2) . ' lokasi)';
                }

                $jenisNama = $a->jenisAstap 
                    ? ($a->jenisAstap->nama_jenis ?: ($a->jenisAstap->uraian_sub_sub_rincian ?: 'PERALATAN DAN MESIN'))
                    : ($a->category === 'ATB' ? 'ASET TAK BERWUJUD' : 'PERALATAN DAN MESIN');

                return [
                    'id'          => $a->id,
                    'kode_barang' => $a->kode_108 ?: ('AST-' . str_pad($a->id, 3, '0', STR_PAD_LEFT)),
                    'nama_barang' => $a->nama_barang,
                    'jenis_nama'  => strtoupper($jenisNama),
                    'tahun'       => (string) $a->tahun_perolehan,
                    'volume'      => $a->jumlah_volume . ' ' . ($a->satuan ?: 'Unit'),
                    'lokasi'      => $lokasiStr,
                    'kondisi'     => $firstReg ? $firstReg->kondisi : 'Baik',
                ];
            });

        // 7. Data Grafik Distribusi per Unit/Ruangan Terbanyak
        $topUnitsDist = AstapRegister::whereNotNull('unit_id')
            ->join('units', 'astap_registers.unit_id', '=', 'units.id')
            ->selectRaw('units.nama as unit_nama, count(*) as total')
            ->groupBy('units.id', 'units.nama')
            ->orderBy('total', 'desc')
            ->limit(6)
            ->get();

        $chartTopUnitLabels = [];
        $chartTopUnitData = [];
        foreach ($topUnitsDist as $u) {
            $chartTopUnitLabels[] = $u->unit_nama;
            $chartTopUnitData[]   = (int) $u->total;
        }

        $belumTerdistribusi = max(0, $totalAsetRegisterCount - $totalTerdistribusiUnit);
        $persenTerdistribusi = $totalAsetRegisterCount > 0 ? round(($totalTerdistribusiUnit / $totalAsetRegisterCount) * 100, 1) : 0;
        $persenBaik = $totalAsetRegisterCount > 0 ? round(($kondisiBaik / $totalAsetRegisterCount) * 100, 1) : 0;

        return [
            'totalAsetVolumeCount'     => $totalAsetVolumeCount,
            'totalAsetRegisterCount'   => $totalAsetRegisterCount,
            'totalAstapMasterCount'    => $totalAstapMasterCount,
            'totalValuasi'             => $totalValuasi,
            'hargaAsetFormatted'       => $hargaAsetFormatted,
            'hargaAsetUnit'            => $hargaAsetUnit,
            'hargaAsetFull'            => $hargaAsetFull,
            'totalUnitRsudCount'       => $totalUnitRsudCount,
            'totalTerdistribusiUnit'   => $totalTerdistribusiUnit,
            'totalTransaksiDistribusi' => $totalTransaksiDistribusi,
            'belumTerdistribusi'       => $belumTerdistribusi,
            'persenTerdistribusi'      => $persenTerdistribusi,
            'persenBaik'               => $persenBaik,
            'kondisiBaik'              => $kondisiBaik,
            'kondisiKurangBaik'        => $kondisiKurangBaik,
            'kondisiRusakRingan'       => $kondisiRusakRingan,
            'kondisiRusakBerat'        => $kondisiRusakBerat,
            'totalRusak'               => $totalRusak,
            'recentAstaps'             => $recentAstaps,
            'chartLabels'              => $chartLabels,
            'chartVolumeData'          => $chartVolumeData,
            'chartHargaDataJuta'       => $chartHargaDataJuta,
            'chartKumulatifVolume'     => $chartKumulatifVolume,
            'chartKumulatifHargaJuta'  => $chartKumulatifHargaJuta,
            'chartKondisiLabels'       => ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'],
            'chartKondisiData'         => [$kondisiBaik, $kondisiKurangBaik, $kondisiRusakRingan, $kondisiRusakBerat],
            'chartDistribusiStatusLabels' => ['Terdistribusi ke Ruangan', 'Belum Didistribusi (Gudang)'],
            'chartDistribusiStatusData'   => [$totalTerdistribusiUnit, $belumTerdistribusi],
            'chartTopUnitLabels'       => $chartTopUnitLabels,
            'chartTopUnitData'         => $chartTopUnitData,
        ];
    }

    /**
     * Dashboard Sub Admin Ruangan
     */
    public function subAdmin()
    {
        $user = Auth::user();
        $unit = null;
        if ($user->unit_id) {
            $unit = \App\Models\Unit::find($user->unit_id);
        }
        
        // Jika sub_admin belum memiliki unit_id, tampilkan dashboard kosong
        // (JANGAN fallback ke unit lain — berbahaya untuk keamanan data)
        $unitId   = $unit ? $unit->id   : null;
        $unitNama = $unit ? $unit->nama  : '';

        // Jika tidak ada unit, kembalikan view dengan data kosong
        if (!$unitId) {
            return view('dashboards.sub_admin', [
                'unit'                        => null,
                'user'                        => $user,
                'distribusisList'             => [],
                'totalAsetCount'              => 0,
                'totalNilaiNum'               => 0,
                'totalNilaiFormatted'         => 'Rp 0',
                'kondisiBaik'                 => 0,
                'kondisiKurangBaik'           => 0,
                'kondisiRusakRingan'          => 0,
                'kondisiRusakBerat'           => 0,
                'totalRusak'                  => 0,
                'attentionAssets'             => [],
                'unitNama'                    => '',
                'chartYears'                  => ['Thn ' . date('Y')],
                'chartRoomVolume'             => [0],
                'chartRoomHarga'              => [0],
                'chartRoomHargaJuta'          => [0],
                'chartRoomKumulatifVolume'    => [0],
                'chartRoomKumulatifHargaJuta' => [0],
            ]);
        }

        // 1. Distribusi data riil khusus unit ini
        $dbDistribusis = \App\Models\Distribusi::with([
                'unit',
                'items.astap.jenisAstap',
                'items.registers.astapRegister'
            ])
            ->where('unit_id', $unitId)
            ->orderBy('id', 'desc')
            ->get();

        $distribusisList = $dbDistribusis->map(function($d) use ($unit, $user) {
            $itemsMapped = $d->items->map(function($it) {
                $spec = is_array($it->astap?->spesifikasi_json)
                    ? $it->astap->spesifikasi_json
                    : (json_decode($it->astap?->spesifikasi_json ?? '', true) ?? []);
                $merk = $spec['merk'] ?? ($spec['type'] ?? ($spec['konstruksi'] ?? '-'));
                
                $nibarList = $it->registers->map(fn($r) => $r->astapRegister?->nibar)->filter()->values()->all();
                $firstKondisi = $it->registers->first()?->astapRegister?->kondisi ?? 'Baik';

                return [
                    'nama'       => $it->astap?->nama_barang ?? 'Barang ASTAP',
                    'merk'       => $merk,
                    'qty'        => $it->qty . ' ' . ($it->astap?->satuan ?: 'Unit'),
                    'kondisi'    => $firstKondisi,
                    'nibar_list' => $nibarList
                ];
            });

            $firstItemName = $itemsMapped->first()['nama'] ?? 'Barang ASTAP';
            $moreCount = $itemsMapped->count() > 1 ? ' + ' . ($itemsMapped->count() - 1) . ' item lainnya' : '';
            $totalVol = $d->items->sum('qty');

            $tglCarbon = $d->tanggal_distribusi;
            $bulanIndo = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $tglStr = $tglCarbon ? ($tglCarbon->day . ' ' . ($bulanIndo[$tglCarbon->month] ?? '') . ' ' . $tglCarbon->year) : '-';

            return [
                'id'         => $d->id,
                'kode'       => $d->kode,
                'bast_nomor' => $d->bast_nomor ?: '-',
                'nama'       => $firstItemName . $moreCount,
                'qty'        => $totalVol . ' Item',
                'tgl'        => $tglStr,
                'status'     => $d->status,
                'keterangan' => $d->keterangan ?: 'Permohonan kebutuhan inventaris ruangan',
                'pengaju'    => $unit?->kepala ?? $user->name,
                'ruangan'    => $unit?->nama ?? 'Ruangan',
                'items'      => $itemsMapped->values()->all()
            ];
        })->values()->all();

        // 2. Data Register Aset di Ruangan Ini — Mendukung unit_id dan ruang_pemegang (sesuai katalog Unit)
        $registers = \App\Models\AstapRegister::with(['astap.jenisAstap'])
            ->where(function($q) use ($unitId, $unitNama) {
                if ($unitId) {
                    $q->where('unit_id', $unitId);
                }
                if ($unitNama && $unitNama !== 'Ruangan Saya') {
                    $q->orWhere(function($q2) use ($unitId, $unitNama) {
                        // Jangan sertakan jika aset sudah tercatat milik unit lain
                        if ($unitId) {
                            $q2->where(function($q3) use ($unitId) {
                                $q3->whereNull('unit_id')->orWhere('unit_id', $unitId);
                            });
                        }
                        $q2->whereNotNull('ruang_pemegang')
                           ->where('ruang_pemegang', '!=', '')
                           ->where(function($q4) use ($unitNama) {
                               $q4->where('ruang_pemegang', $unitNama)
                                  ->orWhere('ruang_pemegang', 'LIKE', '%' . $unitNama . '%');
                           });
                    });
                }
            })
            ->get();

        // Ambil ASTAP yang terhubung langsung via unit_id jika belum masuk dalam register
        $directRegisters = collect();
        if ($unitId) {
            $astapDirectIds = \App\Models\Astap::where('unit_id', $unitId)
                ->whereNotIn('id', $registers->pluck('astap_id')->filter()->unique())
                ->pluck('id');

            if ($astapDirectIds->isNotEmpty()) {
                $directRegisters = \App\Models\AstapRegister::with(['astap.jenisAstap'])
                    ->whereIn('astap_id', $astapDirectIds)
                    ->get();
            }
        }

        $allRegisters = $registers->concat($directRegisters)->unique('id');

        $totalAsetCount = $allRegisters->count();
        $totalNilaiNum = $allRegisters->sum(fn($r) => $r->astap ? (float) ($r->astap->harga_satuan ?: ($r->astap->total_realisasi / max(1, $r->astap->jumlah_volume))) : 0);
        $totalNilaiFormatted = 'Rp ' . number_format($totalNilaiNum, 0, ',', '.');

        $kondisiBaik = $allRegisters->where('kondisi', 'Baik')->count();
        $kondisiKurangBaik = $allRegisters->where('kondisi', 'Kurang Baik')->count();
        $kondisiRusakRingan = $allRegisters->where('kondisi', 'Rusak Ringan')->count();
        $kondisiRusakBerat = $allRegisters->whereIn('kondisi', ['Rusak Berat', 'Rusak'])->count();
        $totalRusak = $kondisiKurangBaik + $kondisiRusakRingan + $kondisiRusakBerat;

        // 3. Aset yang perlu perhatian / rusak di ruangan ini
        $attentionAssets = $allRegisters->filter(fn($r) => $r->kondisi !== 'Baik')->map(function($r) {
            return [
                'id'      => $r->id,
                'kode'    => $r->nibar ?: $r->no_register,
                'nama'    => $r->astap?->nama_barang ?? 'Barang Inventaris',
                'status'  => $r->kondisi,
                'lokasi'  => $r->ruang_pemegang ?: 'Ruangan',
                'catatan' => 'Kondisi fisik unit tercatat: ' . $r->kondisi . ' (Perlu pengecekan berkala / servis)'
            ];
        })->values()->all();

        // 4. Data Agregasi Grafik Nilai Aset Ruangan (Berdasarkan Tahun Perolehan)
        $yearlyMap = [];
        $categoryMap = [];

        foreach ($allRegisters as $r) {
            $astap = $r->astap;
            $year = (int) ($astap?->tahun_perolehan ?: ($astap?->created_at ? $astap->created_at->year : date('Y')));
            if ($year < 1970 || $year > ((int)date('Y') + 1)) {
                $year = (int) date('Y');
            }

            $hargaSatuan = $astap ? (float) ($astap->harga_satuan ?: ($astap->total_realisasi / max(1, $astap->jumlah_volume))) : 0;

            if (!isset($yearlyMap[$year])) {
                $yearlyMap[$year] = ['volume' => 0, 'harga' => 0];
            }
            $yearlyMap[$year]['volume'] += 1;
            $yearlyMap[$year]['harga'] += $hargaSatuan;

            $catName = $astap?->jenisAstap?->nama_jenis ?: ($astap?->category ?: 'Peralatan & Mesin');
            if (!isset($categoryMap[$catName])) {
                $categoryMap[$catName] = ['volume' => 0, 'harga' => 0];
            }
            $categoryMap[$catName]['volume'] += 1;
            $categoryMap[$catName]['harga'] += $hargaSatuan;
        }

        ksort($yearlyMap);

        $chartYears = [];
        $chartRoomVolume = [];
        $chartRoomHarga = [];
        $chartRoomHargaJuta = [];
        $chartRoomKumulatifVolume = [];
        $chartRoomKumulatifHargaJuta = [];

        $runVol = 0;
        $runHarga = 0;

        foreach ($yearlyMap as $yr => $stat) {
            $runVol += $stat['volume'];
            $runHarga += $stat['harga'];

            $chartYears[] = 'Thn ' . $yr;
            $chartRoomVolume[] = $stat['volume'];
            $chartRoomHarga[] = round($stat['harga'], 2);
            $chartRoomHargaJuta[] = round($stat['harga'] / 1000000, 2);
            $chartRoomKumulatifVolume[] = $runVol;
            $chartRoomKumulatifHargaJuta[] = round($runHarga / 1000000, 2);
        }

        if (empty($chartYears)) {
            $chartYears = ['Thn ' . date('Y')];
            $chartRoomVolume = [0];
            $chartRoomHarga = [0];
            $chartRoomHargaJuta = [0];
            $chartRoomKumulatifVolume = [0];
            $chartRoomKumulatifHargaJuta = [0];
        }

        return view('dashboards.sub_admin', [
            'unit'                        => $unit,
            'user'                        => $user,
            'distribusisList'             => $distribusisList,
            'totalAsetCount'              => $totalAsetCount,
            'totalNilaiNum'               => $totalNilaiNum,
            'totalNilaiFormatted'         => $totalNilaiFormatted,
            'kondisiBaik'                 => $kondisiBaik,
            'kondisiKurangBaik'           => $kondisiKurangBaik,
            'kondisiRusakRingan'          => $kondisiRusakRingan,
            'kondisiRusakBerat'           => $kondisiRusakBerat,
            'totalRusak'                  => $totalRusak,
            'attentionAssets'             => $attentionAssets,
            'unitNama'                    => $unitNama,
            // Chart Data
            'chartYears'                  => $chartYears,
            'chartRoomVolume'             => $chartRoomVolume,
            'chartRoomHarga'              => $chartRoomHarga,
            'chartRoomHargaJuta'          => $chartRoomHargaJuta,
            'chartRoomKumulatifVolume'    => $chartRoomKumulatifVolume,
            'chartRoomKumulatifHargaJuta' => $chartRoomKumulatifHargaJuta,
        ]);
    }

    /**
     * Halaman Profil Sub Admin
     */
    public function subAdminProfile()
    {
        return view('pages.subadmin_profile');
    }

    /**
     * Update Profil Sub Admin
     */
    public function updateSubAdminProfile(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $changeType = $request->input('change_type', 'both');
        
        $rules = [];
        $customMessages = [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 4 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ];

        if ($changeType === 'email' || $changeType === 'both') {
            $rules['email'] = [
                'required', 
                'email', 
                'max:255', 
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)
            ];
        }

        if ($changeType === 'password') {
            $rules['password'] = 'required|string|min:4|confirmed';
        } elseif ($changeType === 'both') {
            $rules['password'] = 'nullable|string|min:4|confirmed';
        }

        $validated = $request->validate($rules, $customMessages);

        // 1. Update Akun Pengguna di tabel `users`
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        $user->save();

        // 2. Sinkronkan email ke tabel `units` pada ruangan milik Sub Admin ini jika email diubah
        if (isset($validated['email']) && $user->unit_id) {
            $unit = \App\Models\Unit::find($user->unit_id);
            if ($unit) {
                // Update langsung tanpa trigger loop event
                $unit->withoutEvents(function () use ($unit, $validated) {
                    $unit->update([
                        'email' => $validated['email']
                    ]);
                });
            }
        }

        $msg = match($changeType) {
            'email' => 'Alamat email berhasil diperbarui dan tersinkronisasi ke data unit ruangan!',
            'password' => 'Password akun ruangan berhasil diperbarui!',
            default => 'Perubahan kredensial akun ruangan berhasil disimpan!',
        };

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'email' => $user->email,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}

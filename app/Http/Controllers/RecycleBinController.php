<?php

namespace App\Http\Controllers;

use App\Models\AstapMutasi;
use App\Models\AstapMutasiRegister;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\Distribusi;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecycleBinController extends Controller
{
    /**
     * Tampilkan halaman utama Pusat Data Terhapus (Recycle Bin Center)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->query('tab', 'mutasi');
        if ($activeTab === 'bast') {
            $activeTab = 'mutasi';
        }

        // =========================================================================
        // 1. DATA TERHAPUS: MUTASI ASET
        // =========================================================================
        $rawDeletedMutasis = AstapMutasi::onlyDeleted()
            ->with(['items.register.astap', 'items.register.unit', 'register.astap'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedMutasis = $rawDeletedMutasis->map(function ($m) {
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
                ];
            })->values()->toArray();

            $deletedAt = $m->deleted_at ? Carbon::parse($m->deleted_at) : null;

            return [
                'id'                   => $m->id,
                'kode'                 => $m->nomor_bamb,
                'jenis'                => $m->jenis_mutasi,
                'nama'                 => $itemSummary,
                'kode_barang'          => $firstRegister?->nibar ?? '-',
                'item_count'           => $itemCount,
                'items'                => $itemsMapped,
                'asal'                 => $m->ruangan_asal,
                'tujuan'               => $m->ruangan_tujuan,
                'pemohon'              => $m->penanggung_jawab_asal,
                'penerima_pj'          => $m->penanggung_jawab_tujuan,
                'status_terakhir'      => $m->status,
                'alasan_mutasi'        => $m->alasan_mutasi ?: '-',
                'deleted_by'           => $m->deleted_by ?: 'Administrator',
                'deleted_at'           => $deletedAt ? $deletedAt->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative'  => $deletedAt ? $deletedAt->diffForHumans() : '-',
                'deleted_at_raw'       => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 2. DATA TERHAPUS: MASTER ASTAP
        // =========================================================================
        $rawDeletedAstaps = Astap::onlyDeleted()
            ->with(['jenisAstap', 'rekeningBelanja', 'registers.unit'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedAstaps = $rawDeletedAstaps->map(function ($a) {
            $regCount = $a->registers->count();
            $firstReg = $a->registers->first();
            $deletedAt = $a->deleted_at ? Carbon::parse($a->deleted_at) : null;

            $registersMapped = $a->registers->map(function ($r, $idx) {
                return [
                    'no'          => $idx + 1,
                    'id'          => $r->id,
                    'nibar'       => $r->nibar ?: ($r->no_register ?: '-'),
                    'ruang'       => $r->ruang_pemegang ?: ($r->unit?->nama ?: 'Gudang Perbekalan'),
                    'kondisi'     => $r->kondisi ?: 'Baik',
                    'status'      => $r->status ?: 'Tersedia',
                ];
            })->values()->toArray();

            return [
                'id'                  => $a->id,
                'kode'                => $a->kode_108 ?: ($a->spk_nomor ?: 'ASTAP-' . $a->id),
                'nama'                => $a->nama_barang ?: 'Aset Tetap',
                'tahun'               => $a->tahun_perolehan ?: '-',
                'volume'              => $a->jumlah_volume . ' ' . ($a->satuan ?: 'Unit'),
                'total_realisasi'     => 'Rp ' . number_format($a->total_realisasi ?: ($a->jumlah_anggaran ?: 0), 0, ',', '.'),
                'harga_satuan'        => 'Rp ' . number_format($a->harga_satuan ?: 0, 0, ',', '.'),
                'category'            => $a->category ?: 'KIB B',
                'penyedia'            => $a->penyedia_nama ?: '-',
                'spk_nomor'           => $a->spk_nomor ?: '-',
                'rekening'            => $a->rekeningBelanja?->nama_belanja ?: '-',
                'item_count'          => $regCount,
                'items'               => $registersMapped,
                'deleted_by'          => $a->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 3. DATA TERHAPUS: DISTRIBUSI ASET
        // =========================================================================
        $rawDeletedDistribusis = Distribusi::onlyDeleted()
            ->with(['unit', 'items.astap', 'items.registers.astapRegister'])
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedDistribusis = $rawDeletedDistribusis->map(function ($d) {
            $deletedAt = $d->deleted_at ? Carbon::parse($d->deleted_at) : null;
            $itemsMapped = $d->items->map(function ($it, $idx) {
                $nibars = $it->registers->map(fn($r) => $r->astapRegister?->nibar ?: '-')->filter()->implode(', ');
                return [
                    'no'          => $idx + 1,
                    'nama_barang' => $it->astap?->nama_barang ?: 'Barang Distribusi',
                    'qty'         => $it->qty . ' ' . ($it->astap?->satuan ?: 'Unit'),
                    'nibar_list'  => $nibars ?: '-',
                    'keterangan'  => $it->keterangan ?: '-',
                ];
            })->values()->toArray();

            return [
                'id'                  => $d->id,
                'kode'                => $d->kode,
                'bast_nomor'          => $d->bast_nomor ?: '-',
                'tujuan'              => $d->unit?->nama ?: ($d->tujuan ?: '-'),
                'tanggal'             => $d->tanggal_distribusi ? Carbon::parse($d->tanggal_distribusi)->translatedFormat('d M Y') : '-',
                'status'              => $d->status ?: 'Draft',
                'item_count'          => $d->items->count(),
                'total_qty'           => $d->total_qty . ' Unit',
                'items'               => $itemsMapped,
                'deleted_by'          => $d->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });



        // =========================================================================
        // 5. DATA TERHAPUS: UNIT & PAVILIUN
        // =========================================================================
        $rawDeletedUnits = Unit::onlyDeleted()
            ->with('user')
            ->latest('deleted_at')
            ->latest('id')
            ->get();

        $deletedUnits = $rawDeletedUnits->map(function ($u) {
            $deletedAt = $u->deleted_at ? Carbon::parse($u->deleted_at) : null;
            return [
                'id'                  => $u->id,
                'kode'                => $u->kode_unit ?: 'UNIT-' . $u->id,
                'nama'                => $u->nama,
                'tipe'                => $u->tipe ?: 'Ruangan / Instalasi',
                'kepala'              => $u->kepala ?: '-',
                'nip'                 => $u->nip ?: '-',
                'email'               => $u->email ?: ($u->user?->email ?: '-'),
                'total_aset'          => $u->total_aset ?: 0,
                'deleted_by'          => $u->deleted_by ?: 'Administrator',
                'deleted_at'          => $deletedAt ? $deletedAt->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                'deleted_at_relative' => $deletedAt ? $deletedAt->diffForHumans() : '-',
                'deleted_at_raw'      => $deletedAt ? $deletedAt->toIso8601String() : null,
            ];
        });

        // =========================================================================
        // 5. STATISTIK TERPUSAT SELURUH MODUL
        // =========================================================================
        $now = now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sevenDaysAgo  = $now->copy()->subDays(7);

        $mutasiCount     = $deletedMutasis->count();
        $astapCount      = $deletedAstaps->count();
        $distribusiCount = $deletedDistribusis->count();
        $unitCount       = $deletedUnits->count();

        $totalAllDeleted = $mutasiCount + $astapCount + $distribusiCount + $unitCount;

        // Hitung 30 hari terakhir
        $filterMonth = fn($col) => $col->filter(fn($m) => $m->deleted_at && Carbon::parse($m->deleted_at)->gte($thirtyDaysAgo))->count();
        $totalThisMonth = $filterMonth($rawDeletedMutasis)
            + $filterMonth($rawDeletedAstaps)
            + $filterMonth($rawDeletedDistribusis)
            + $filterMonth($rawDeletedUnits);

        // Hitung 7 hari terakhir
        $filterWeek = fn($col) => $col->filter(fn($m) => $m->deleted_at && Carbon::parse($m->deleted_at)->gte($sevenDaysAgo))->count();
        $totalThisWeek = $filterWeek($rawDeletedMutasis)
            + $filterWeek($rawDeletedAstaps)
            + $filterWeek($rawDeletedDistribusis)
            + $filterWeek($rawDeletedUnits);

        $moduleStats = [
            'mutasi' => [
                'name'  => 'Mutasi Aset',
                'icon'  => '🔄',
                'count' => $mutasiCount,
                'color' => 'amber',
                'ready' => true,
            ],
            'astap' => [
                'name'  => 'Master ASTAP',
                'icon'  => '📦',
                'count' => $astapCount,
                'color' => 'emerald',
                'ready' => true,
            ],
            'distribusi' => [
                'name'  => 'Distribusi Aset',
                'icon'  => '🚚',
                'count' => $distribusiCount,
                'color' => 'teal',
                'ready' => true,
            ],
            'unit' => [
                'name'  => 'Unit & Paviliun',
                'icon'  => '🏥',
                'count' => $unitCount,
                'color' => 'indigo',
                'ready' => true,
            ],
        ];

        return view('pages.recycle_bin', compact(
            'deletedMutasis',
            'deletedAstaps',
            'deletedDistribusis',
            'deletedUnits',
            'activeTab',
            'moduleStats',
            'totalAllDeleted',
            'totalThisMonth',
            'totalThisWeek'
        ));
    }

    /**
     * Pulihkan 1 data berdasarkan modul (Restore Single)
     */
    public function restore(Request $request, string $module, int $id)
    {
        switch ($module) {
            case 'mutasi':
                $mutasi = AstapMutasi::findOrFail($id);
                $bamb = $mutasi->nomor_bamb;
                $mutasi->restoreData();
                $msg = "Data Berita Acara Mutasi {$bamb} berhasil dipulihkan ke status aktif.";
                break;

            case 'astap':
                $astap = Astap::findOrFail($id);
                $nama = $astap->nama_barang ?: 'Aset ASTAP';
                DB::transaction(function () use ($astap) {
                    $astap->restoreData();
                    $astap->registers()->update([
                        'is_deleted'    => 0,
                        'deleted_by'    => null,
                        'deleted_by_id' => null,
                        'deleted_at'    => null,
                    ]);
                });
                $msg = "Data Master Aset ASTAP \"{$nama}\" dan seluruh register NIBAR berhasil dipulihkan.";
                break;

            case 'distribusi':
                $distribusi = Distribusi::findOrFail($id);
                $kode = $distribusi->kode;
                DB::transaction(function () use ($distribusi) {
                    $distribusi->restoreData();
                    // Kembalikan register terkait ke unit jika status terdistribusi
                    if (in_array($distribusi->status, ['Dalam Pengiriman', 'Telah Diterima', 'Diterima'])) {
                        foreach ($distribusi->items as $it) {
                            $regIds = $it->registers->pluck('astap_register_id')->filter()->toArray();
                            if (!empty($regIds) && $distribusi->unit_id) {
                                AstapRegister::whereIn('id', $regIds)->update([
                                    'unit_id'        => $distribusi->unit_id,
                                    'ruang_pemegang' => $distribusi->unit?->nama ?? 'Ruangan Unit',
                                    'status'         => 'Terdistribusi',
                                ]);
                            }
                        }
                    }
                });
                $msg = "Transaksi Distribusi Aset {$kode} berhasil dipulihkan ke status aktif.";
                break;



            case 'unit':
                $unit = Unit::findOrFail($id);
                $nama = $unit->nama;
                $unit->restoreData();
                $msg = "Data Unit / Paviliun \"{$nama}\" berhasil dipulihkan ke katalog aktif.";
                break;

            default:
                return back()->with('error', "Modul {$module} tidak dikenal untuk pemulihan data.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module])->with('success', $msg);
    }

    /**
     * Pulihkan banyak data sekaligus (Bulk Restore)
     */
    public function bulkRestore(Request $request, string $module)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            $msg = 'Pilih minimal satu data yang ingin dipulihkan.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $restoredCount = 0;
        switch ($module) {
            case 'mutasi':
                $items = AstapMutasi::whereIn('id', $ids)->get();
                foreach ($items as $item) {
                    $item->restoreData();
                    $restoredCount++;
                }
                $msg = "Sebanyak {$restoredCount} transaksi mutasi berhasil dipulihkan ke status aktif.";
                break;

            case 'astap':
                $astaps = Astap::whereIn('id', $ids)->get();
                DB::transaction(function () use ($astaps, &$restoredCount) {
                    foreach ($astaps as $a) {
                        $a->restoreData();
                        $a->registers()->update([
                            'is_deleted'    => 0,
                            'deleted_by'    => null,
                            'deleted_by_id' => null,
                            'deleted_at'    => null,
                        ]);
                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} data Master ASTAP berhasil dipulihkan ke status aktif.";
                break;

            case 'distribusi':
                $distribusis = Distribusi::whereIn('id', $ids)->get();
                DB::transaction(function () use ($distribusis, &$restoredCount) {
                    foreach ($distribusis as $d) {
                        $d->restoreData();
                        $restoredCount++;
                    }
                });
                $msg = "Sebanyak {$restoredCount} transaksi distribusi berhasil dipulihkan ke status aktif.";
                break;



            case 'unit':
                $units = Unit::whereIn('id', $ids)->get();
                foreach ($units as $u) {
                    $u->restoreData();
                    $restoredCount++;
                }
                $msg = "Sebanyak {$restoredCount} Unit & Paviliun berhasil dipulihkan ke katalog aktif.";
                break;

            default:
                return back()->with('error', "Modul {$module} tidak dikenal untuk pemulihan massal.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'count' => $restoredCount]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module])->with('success', $msg);
    }

    /**
     * Hapus permanen massal untuk item terpilih (Bulk Force Delete)
     */
    public function bulkForceDelete(Request $request, string $module)
    {
        $userRole = Auth::user()->role ?? 'user';
        if (!in_array($userRole, ['admin', 'master_admin'])) {
            $msg = 'Hanya Administrator yang memiliki wewenang untuk menghapus data permanen.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu data untuk dihapus permanen.');
        }

        $deletedCount = 0;
        switch ($module) {
            case 'mutasi':
                $mutasis = AstapMutasi::whereIn('id', $ids)->get();
                foreach ($mutasis as $m) {
                    AstapMutasiRegister::where('astap_mutasi_id', $m->id)->delete();
                    $m->delete();
                    $deletedCount++;
                }
                $msg = "Sebanyak {$deletedCount} data mutasi telah dihapus permanen dari database.";
                break;

            case 'astap':
                $astaps = Astap::whereIn('id', $ids)->get();
                DB::transaction(function () use ($astaps, &$deletedCount) {
                    foreach ($astaps as $a) {
                        $a->registers()->delete();
                        $a->delete();
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} data Master ASTAP telah dihapus permanen dari database.";
                break;

            case 'distribusi':
                $distribusis = Distribusi::whereIn('id', $ids)->get();
                DB::transaction(function () use ($distribusis, &$deletedCount) {
                    foreach ($distribusis as $d) {
                        $d->delete();
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} transaksi distribusi telah dihapus permanen dari database.";
                break;

            case 'unit':
                $units = Unit::whereIn('id', $ids)->get();
                DB::transaction(function () use ($units, &$deletedCount) {
                    foreach ($units as $u) {
                        User::where('unit_id', $u->id)->delete();
                        $u->delete();
                        $deletedCount++;
                    }
                });
                $msg = "Sebanyak {$deletedCount} Unit & Paviliun telah dihapus permanen dari database.";
                break;

            default:
                return back()->with('error', "Modul {$module} tidak dikenal.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'count' => $deletedCount]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module])->with('success', $msg);
    }

    /**
     * Hapus permanen dari database (Hard Delete - Khusus Admin / Master Admin)
     */
    public function forceDelete(Request $request, string $module, int $id)
    {
        $userRole = Auth::user()->role ?? 'user';
        if (!in_array($userRole, ['admin', 'master_admin'])) {
            $msg = 'Hanya Administrator yang memiliki wewenang untuk menghapus data secara permanen.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        switch ($module) {
            case 'mutasi':
                $mutasi = AstapMutasi::findOrFail($id);
                $bamb = $mutasi->nomor_bamb;
                AstapMutasiRegister::where('astap_mutasi_id', $mutasi->id)->delete();
                $mutasi->delete();
                $msg = "Data Berita Acara Mutasi {$bamb} telah dihapus secara permanen dari database.";
                break;

            case 'astap':
                $astap = Astap::findOrFail($id);
                $nama = $astap->nama_barang ?: 'Aset ASTAP';
                DB::transaction(function () use ($astap) {
                    $astap->registers()->delete();
                    $astap->delete();
                });
                $msg = "Data Master ASTAP \"{$nama}\" telah dihapus secara permanen dari database.";
                break;

            case 'distribusi':
                $distribusi = Distribusi::findOrFail($id);
                $kode = $distribusi->kode;
                DB::transaction(function () use ($distribusi) {
                    $distribusi->delete();
                });
                $msg = "Transaksi Distribusi {$kode} telah dihapus secara permanen dari database.";
                break;



            case 'unit':
                $unit = Unit::findOrFail($id);
                $nama = $unit->nama;
                DB::transaction(function () use ($unit) {
                    User::where('unit_id', $unit->id)->delete();
                    $unit->delete();
                });
                $msg = "Data Unit \"{$nama}\" dan akun terkait telah dihapus secara permanen dari database.";
                break;

            default:
                return back()->with('error', "Modul {$module} tidak dikenal untuk penghapusan permanen.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module])->with('success', $msg);
    }
}

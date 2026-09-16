<?php

namespace App\Http\Controllers;

use App\Models\AstapMutasi;
use App\Models\AstapMutasiRegister;
use App\Models\Astap;
use App\Models\Distribusi;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 1. Ambil data mutasi yang terhapus
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

        // 2. Hitung statistik terpusat
        $now = now();
        $mutasiCount = $deletedMutasis->count();
        $mutasiMonthCount = $rawDeletedMutasis->filter(fn($m) => $m->deleted_at && Carbon::parse($m->deleted_at)->gte($now->copy()->subDays(30)))->count();
        $mutasiWeekCount = $rawDeletedMutasis->filter(fn($m) => $m->deleted_at && Carbon::parse($m->deleted_at)->gte($now->copy()->subDays(7)))->count();

        // Hitungan per modul (disiapkan untuk modul lain saat kolom soft delete ditambahkan)
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
                'count' => 0,
                'color' => 'emerald',
                'ready' => false,
            ],
            'distribusi' => [
                'name'  => 'Distribusi Aset',
                'icon'  => '🚚',
                'count' => 0,
                'color' => 'teal',
                'ready' => false,
            ],
            'bast' => [
                'name'  => 'Berita Acara (BAST)',
                'icon'  => '📜',
                'count' => 0,
                'color' => 'blue',
                'ready' => false,
            ],
            'unit' => [
                'name'  => 'Unit & Paviliun',
                'icon'  => '🏥',
                'count' => 0,
                'color' => 'indigo',
                'ready' => false,
            ],
        ];

        $totalAllDeleted = $mutasiCount;
        $totalThisMonth = $mutasiMonthCount;
        $totalThisWeek  = $mutasiWeekCount;

        return view('pages.recycle_bin', compact(
            'deletedMutasis',
            'activeTab',
            'moduleStats',
            'totalAllDeleted',
            'totalThisMonth',
            'totalThisWeek'
        ));
    }

    /**
     * Pulihkan 1 data berdasarkan modul
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

            default:
                return back()->with('error', "Modul {$module} belum didukung untuk pemulihan data.");
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

            default:
                return back()->with('error', "Modul {$module} belum didukung untuk pemulihan massal.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'count' => $restoredCount]);
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

            default:
                return back()->with('error', "Modul {$module} belum didukung untuk penghapusan permanen.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module])->with('success', $msg);
    }

    /**
     * Kosongkan seluruh tong sampah untuk modul tertentu (Empty Trash - Khusus Master Admin)
     */
    public function emptyTrash(Request $request, string $module)
    {
        $userRole = Auth::user()->role ?? 'user';
        if ($userRole !== 'master_admin') {
            $msg = 'Hanya Master Admin yang memiliki wewenang untuk mengosongkan seluruh tong sampah.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $deletedCount = 0;
        switch ($module) {
            case 'mutasi':
                $mutasis = AstapMutasi::onlyDeleted()->get();
                foreach ($mutasis as $m) {
                    AstapMutasiRegister::where('astap_mutasi_id', $m->id)->delete();
                    $m->delete();
                    $deletedCount++;
                }
                $msg = "Seluruh data mutasi terhapus ({$deletedCount} transaksi) telah dikosongkan secara permanen.";
                break;

            default:
                return back()->with('error', "Modul {$module} belum didukung.");
        }

        session()->flash('success', $msg);
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'count' => $deletedCount]);
        }

        return redirect()->route('recycle_bin.index', ['tab' => $module])->with('success', $msg);
    }
}

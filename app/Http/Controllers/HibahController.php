<?php

namespace App\Http\Controllers;

use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\AstapHibah;
use App\Models\JenisAstap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HibahController extends Controller
{
    /**
     * Tampilkan Halaman Utama Master Data Hibah Aset (Masuk & Keluar)
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $filterTipe = $request->query('tipe', 'all'); // 'all', 'masuk', 'keluar'
        $filterTahun = $request->query('tahun', 'all');
        $filterTw = $request->query('triwulan', 'all');
        $search = trim($request->query('search', ''));

        // Query utama riwayat hibah
        $query = AstapHibah::with(['astap.jenisAstap', 'astap.registers', 'register', 'user'])
            ->orderBy('tanggal_bast', 'desc')
            ->orderBy('id', 'desc');

        if ($filterTipe !== 'all') {
            $query->where('tipe_hibah', $filterTipe);
        }

        if ($filterTahun !== 'all') {
            $query->where('tahun', $filterTahun);
        }

        if ($filterTw !== 'all') {
            $query->where('triwulan', $filterTw);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pihak_hibah', 'like', "%{$search}%")
                  ->orWhere('nomor_bast', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('astap', function ($sq) use ($search) {
                      $sq->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_108', 'like', "%{$search}%");
                  });
            });
        }

        $hibahRecords = $query->get();

        // Hitung Statistik KPI
        $allHibahs = AstapHibah::with('astap')->get();
        $totalMasukUnit = $allHibahs->where('tipe_hibah', 'masuk')->sum('jumlah_volume');
        $totalMasukNominal = $allHibahs->where('tipe_hibah', 'masuk')->sum('nilai_aset');
        $totalKeluarUnit = $allHibahs->where('tipe_hibah', 'keluar')->sum('jumlah_volume');
        $totalKeluarNominal = $allHibahs->where('tipe_hibah', 'keluar')->sum('nilai_aset');

        // Daftar Aset Aktif yang dapat dihibahkan keluar (berstatus 'Tersedia' atau 'Aktif', belum dihibahkan/dihapus)
        $activeAstaps = Astap::where('is_deleted', 0)
            ->whereHas('registers', function ($q) {
                $q->where('is_deleted', 0)
                  ->whereIn('status', ['Tersedia', 'Aktif']);
            })
            ->with(['registers' => function ($q) {
                $q->where('is_deleted', 0)
                  ->whereIn('status', ['Tersedia', 'Aktif']);
            }, 'jenisAstap'])
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function ($a) {
                $availRegisters = $a->registers;
                $availCount = $availRegisters->count();
                $originalVol = max(1, (int) $a->jumlah_volume);

                return [
                    'id' => $a->id,
                    'nama_barang' => $a->nama_barang,
                    'kode_barang' => $a->kode_108 ?: ($a->jenisAstap ? $a->jenisAstap->sub_sub_rincian_objek : '-'),
                    'category' => $a->category,
                    'tahun_perolehan' => $a->tahun_perolehan,
                    'jumlah_volume' => $availCount,
                    'satuan' => $a->satuan ?: 'Unit',
                    'harga_satuan' => (float) ($a->harga_satuan ?: ($a->total_realisasi / $originalVol)),
                    'total_realisasi' => (float) $a->total_realisasi,
                    'registers' => $availRegisters->map(function ($r) {
                        return [
                            'id' => $r->id,
                            'nibar' => $r->nibar ?: $r->no_register,
                            'ruang' => $r->ruang_pemegang ?: '-',
                            'kondisi' => $r->kondisi ?: 'Baik',
                            'status' => $r->status,
                        ];
                    })->values()->toArray(),
                ];
            });

        // Daftar Tahun Unik untuk Filter
        $availableYears = AstapHibah::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (!in_array(date('Y'), $availableYears)) {
            array_unshift($availableYears, (int) date('Y'));
        }

        return view('pages.hibah.index', compact(
            'hibahRecords',
            'totalMasukUnit',
            'totalMasukNominal',
            'totalKeluarUnit',
            'totalKeluarNominal',
            'activeAstaps',
            'availableYears',
            'filterTipe',
            'filterTahun',
            'filterTw',
            'search'
        ));
    }

    /**
     * Simpan Transaksi Hibah Keluar (Pengurangan Barang RSUD yang dihibahkan ke luar)
     */
    public function storeHibahKeluar(Request $request)
    {
        // Normalisasi tanggal_bast jika format dd/mm/yyyy
        if ($request->has('tanggal_bast') && is_string($request->tanggal_bast)) {
            $rawDate = trim($request->tanggal_bast);
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $rawDate, $m)) {
                $isoDate = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
                $request->merge(['tanggal_bast' => $isoDate]);
                if ($request->isJson()) {
                    $request->json()->set('tanggal_bast', $isoDate);
                }
            }
        }

        $data = $request->validate([
            'astap_id'        => 'required|integer|exists:astaps,id',
            'register_ids'    => 'nullable|array',
            'register_ids.*'  => 'integer|exists:astap_registers,id',
            'penerima_hibah'  => 'required|string|max:255',
            'nomor_bast'      => 'required|string|max:150',
            'tanggal_bast'    => 'required|date',
            'nilai_aset'      => 'required|numeric|min:0',
            'tahun'           => 'required|integer',
            'triwulan'        => 'required|string|in:TW I,TW II,TW III,TW IV',
            'keterangan'      => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $astap = Astap::with('registers')->findOrFail($data['astap_id']);

        $selectedRegisterIds = $data['register_ids'] ?? [];
        if (count($selectedRegisterIds) > 0) {
            $selectedRegisterIds = AstapRegister::whereIn('id', $selectedRegisterIds)
                ->where('astap_id', $astap->id)
                ->where(function ($q) {
                    $q->where('status', 'Tersedia')
                      ->orWhere('status', 'Aktif')
                      ->orWhereNull('status');
                })
                ->where('is_deleted', 0)
                ->pluck('id')
                ->toArray();
        }
        $volumeKeluar = count($selectedRegisterIds) > 0 ? count($selectedRegisterIds) : max(1, (int) $astap->jumlah_volume);

        DB::transaction(function () use ($data, $user, $astap, $selectedRegisterIds, $volumeKeluar) {
            // 1. Buat catatan di astap_hibahs (tipe keluar)
            $firstRegId = count($selectedRegisterIds) === 1 ? $selectedRegisterIds[0] : null;

            AstapHibah::create([
                'tipe_hibah'        => 'keluar',
                'astap_id'          => $astap->id,
                'astap_register_id' => $firstRegId,
                'pihak_hibah'       => $data['penerima_hibah'],
                'nomor_bast'        => $data['nomor_bast'],
                'tanggal_bast'      => $data['tanggal_bast'],
                'jumlah_volume'     => $volumeKeluar,
                'satuan'            => $astap->satuan ?: 'Unit',
                'nilai_aset'        => (float) $data['nilai_aset'],
                'tahun'             => (int) $data['tahun'],
                'triwulan'          => $data['triwulan'],
                'keterangan'        => $data['keterangan'] ?? null,
                'user_id'           => $user?->id,
            ]);

            // 2. Tandai unit register yang dihibahkan menjadi tidak aktif / pengurangan
            $alasan = 'Dihibahkan ke ' . $data['penerima_hibah'] . ' (BAST: ' . $data['nomor_bast'] . ')';

            if (count($selectedRegisterIds) > 0) {
                AstapRegister::whereIn('id', $selectedRegisterIds)->update([
                    'status'        => 'Dihibahkan',
                    'is_deleted'    => 1,
                    'deleted_at'    => now(),
                    'deleted_by'    => $user?->name ?: 'Administrator',
                ]);

                // Cek sisa register aktif pada astap induk
                $sisaAktif = AstapRegister::where('astap_id', $astap->id)
                    ->where('is_deleted', 0)
                    ->count();

                if ($sisaAktif === 0) {
                    $astap->update([
                        'is_deleted'          => 1,
                        'deleted_at'          => now(),
                        'deleted_by'          => $user?->name ?: 'Administrator',
                        'keterangan_tambahan' => $alasan,
                    ]);
                } else {
                    $astap->update([
                        'jumlah_volume' => $sisaAktif,
                    ]);
                }
            } else {
                // Seluruh register dihibahkan
                AstapRegister::where('astap_id', $astap->id)->update([
                    'status'        => 'Dihibahkan',
                    'is_deleted'    => 1,
                    'deleted_at'    => now(),
                    'deleted_by'    => $user?->name ?: 'Administrator',
                ]);

                $astap->update([
                    'is_deleted'          => 1,
                    'deleted_at'          => now(),
                    'deleted_by'          => $user?->name ?: 'Administrator',
                    'keterangan_tambahan' => $alasan,
                ]);
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Barang "' . $astap->nama_barang . '" berhasil diproses hibah keluar (dihibahkan ke ' . $data['penerima_hibah'] . ')!',
            ]);
        }

        return redirect()->route('master.hibah')
            ->with('success', 'Barang "' . $astap->nama_barang . '" berhasil dihibahkan ke ' . $data['penerima_hibah'] . '.');
    }

    /**
     * Batalkan / Hapus Transaksi Hibah
     */
    public function destroy($id)
    {
        $hibah = AstapHibah::findOrFail($id);

        DB::transaction(function () use ($hibah) {
            // Jika hibah keluar dibatalkan/dihapus, pulihkan unit barang di ASTAP & register
            if ($hibah->tipe_hibah === 'keluar') {
                $astap = Astap::find($hibah->astap_id);

                // Ambil register yang dihibahkan
                if ($hibah->astap_register_id) {
                    $regs = AstapRegister::where('id', $hibah->astap_register_id)->get();
                } else {
                    $regs = AstapRegister::where('astap_id', $hibah->astap_id)
                        ->where('status', 'Dihibahkan')
                        ->get();
                }

                foreach ($regs as $reg) {
                    $statusBalik = (!empty($reg->ruang_pemegang) && !in_array($reg->ruang_pemegang, ['-', 'Belum Ditempatkan / Di Gudang', 'Gudang Aset']))
                        ? 'Aktif'
                        : 'Tersedia';

                    $reg->update([
                        'status'       => $statusBalik,
                        'is_deleted'   => 0,
                        'deleted_at'   => null,
                        'deleted_by'   => null,
                    ]);
                }

                if ($astap) {
                    $sisaAktif = AstapRegister::where('astap_id', $astap->id)
                        ->where('is_deleted', 0)
                        ->count();

                    $astap->update([
                        'is_deleted'          => 0,
                        'deleted_at'          => null,
                        'deleted_by'          => null,
                        'keterangan_tambahan' => null,
                        'jumlah_volume'       => max(1, $sisaAktif),
                    ]);
                }
            }

            // Hapus record riwayat transaksi hibah (Hard Delete pada tabel astap_hibahs)
            $hibah->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Catatan riwayat hibah berhasil dihapus.',
        ]);
    }

    /**
     * Cetak Lembar Berita Acara Serah Terima (BAST) Hibah Aset Resmi
     */
    public function cetak($id)
    {
        Carbon::setLocale('id');

        $hibah = AstapHibah::with([
            'astap.jenisAstap',
            'astap.registers',
            'register',
            'user',
        ])->findOrFail($id);

        return view('pages.hibah.cetak_bast', compact('hibah'));
    }
}


<!-- ========================================================================= -->
<!-- TABEL DATA MASTER BELANJA BARANG MULTI-ITEM (INSTALASI PERBEKALAN)        -->
<!-- ========================================================================= -->
<style>
    /* Freeze Sticky Column Aksi Belanja Barang (Opaque & Anti-Bocor saat Scroll Horizontal) */
    .sticky-aksi-belanja {
        position: sticky !important;
        right: 0 !important;
        z-index: 10 !important;
        background-color: #0f172a !important;
        box-shadow: -6px 0 12px rgba(0, 0, 0, 0.6) !important;
    }
    tr:hover .sticky-aksi-belanja {
        background-color: #1e293b !important;
    }
    th.sticky-aksi-belanja {
        position: sticky !important;
        top: 0 !important;
        right: 0 !important;
        z-index: 20 !important;
        background-color: #020617 !important;
    }
</style>

<div class="rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl overflow-hidden">
    <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                <span>📋 Daftar Faktur Belanja Barang &amp; Perbekalan Ruangan</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Total {{ count($belanjaBarangRecords ?? []) }} faktur perolehan belanja barang tercatat dalam sistem SIMAT-RK.
            </p>
        </div>

        <span class="text-[11px] font-mono font-bold text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-xl border border-indigo-500/30">
            Perbekalan &amp; Operasional
        </span>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-w-[940px]">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800 font-extrabold select-none">
                <tr>
                    <th class="py-3 px-2 w-10 text-center bg-slate-950">No</th>
                    <th class="py-3 px-3 w-[195px] bg-slate-950">Dokumen Faktur &amp; Toko</th>
                    <th class="py-3 px-3 min-w-[200px] bg-slate-950">Rincian Barang (Akun 108)</th>
                    <th class="py-3 px-3 w-32 text-center bg-slate-950">Volume / Kuantitas</th>
                    <th class="py-3 px-3 w-[165px] text-right whitespace-nowrap bg-slate-950">Total Pembelian (Rp)</th>
                    <th class="py-3 px-3 w-[230px] min-w-[230px] text-center whitespace-nowrap border-l border-slate-800 shrink-0 sticky-aksi-belanja">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($belanjaBarangRecords ?? [] as $idx => $row)
                    @php
                        $astaps = $row->astaps;
                        if ($astaps->isEmpty() && $row->astap) {
                            $astaps = collect([$row->astap]);
                        }
                        $firstAstap = $astaps->first();
                        $firstReg = $firstAstap?->registers?->first();
                        $totalItemCount = $astaps->count();
                        $totalVolFaktur = (int) $astaps->sum('jumlah_volume');
                        $categories = $astaps->map(fn($a) => $a->category)->filter()->unique()->values();
                        $primaryCategory = $categories->count() === 1 ? $categories->first() : ($categories->isNotEmpty() ? $categories->first() : ($firstAstap?->category ?: 'KIB B'));

                        // Data untuk modal detail
                        $itemData = [
                            'id'                   => $row->id,
                            'nomor_faktur'         => $row->nomor_faktur,
                            'tanggal_faktur'       => $row->tanggal_faktur ? \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('d F Y') : '-',
                            'tanggal_faktur_raw'   => $row->tanggal_faktur,
                            'toko_penyedia'        => $row->toko_penyedia,
                            'total_pembelian'      => (float) $row->total_pembelian,
                            'status_alokasi'       => $row->status_alokasi ?: 'gudang',
                            'keterangan'           => $row->keterangan ?: '-',
                            'dokumen_faktur_url'   => $row->dokumen_faktur_url,
                            'dokumen_faktur_name'  => $row->dokumen_faktur ? basename($row->dokumen_faktur) : null,
                            'dokumen_kuitansi_url' => $row->dokumen_kuitansi_url,
                            'dokumen_kuitansi_name'=> $row->dokumen_kuitansi ? basename($row->dokumen_kuitansi) : null,
                            'total_item_count'     => $totalItemCount,
                            'total_volume'         => $totalVolFaktur,
                            'category'             => $primaryCategory,
                            'categories'           => $categories,
                            'first_nama'           => $firstAstap?->nama_barang,
                            'tahun'                => $firstAstap?->tahun_perolehan ?: date('Y'),
                            'triwulan'             => $firstAstap?->triwulan ?: 'TW I',
                            'items'                => $astaps->map(function ($a) {
                                return [
                                    'id'            => $a->id,
                                    'nama_barang'   => $a->nama_barang,
                                    'kode_108'      => $a->kode_108 ?: ($a->jenisAstap?->sub_sub_rincian_objek ?: '-'),
                                    'nama_108'      => $a->jenisAstap?->uraian_sub_sub_rincian ?: ($a->jenisAstap?->nama_jenis ?: 'Aset Operasional'),
                                    'category'      => $a->category ?: 'KIB B',
                                    'jumlah_volume' => (int) $a->jumlah_volume,
                                    'satuan'        => $a->satuan ?: 'Unit',
                                    'harga_satuan'  => (float) $a->harga_satuan,
                                    'subtotal'      => (float) $a->total_realisasi,
                                    'ruangan'       => $a->unit?->nama ?: ($a->registers?->first()?->ruang_pemegang ?: 'Belum Ditempatkan'),
                                    'kondisi'       => $a->registers?->first()?->kondisi ?: 'Baik',
                                    'status'        => $a->registers?->first()?->status ?: 'Tersedia',
                                    'registers'     => $a->registers->map(fn($r) => [
                                        'id'      => $r->id,
                                        'nibar'   => $r->nibar,
                                        'kondisi' => $r->kondisi ?: 'Baik',
                                        'status'  => $r->status,
                                        'ruang'   => $r->ruang_pemegang ?: ($r->unit?->nama ?: 'Belum Ditempatkan'),
                                        'qr_url'  => url('/scan/' . $r->nibar),
                                    ])->values()
                                ];
                            })->values()
                        ];
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors group">
                        <!-- 1. Nomor -->
                        <td class="py-3.5 px-2 text-center font-mono text-slate-500 text-xs">
                            {{ $idx + 1 }}
                        </td>

                        <!-- 2. Dokumen Faktur & Toko -->
                        <td class="py-3.5 px-3">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="text-xs font-bold text-white truncate max-w-[195px]" title="{{ $row->toko_penyedia }}">
                                    🏪 {{ $row->toko_penyedia }}
                                </span>
                            </div>
                            <div class="text-[11px] font-mono text-slate-300 truncate max-w-[195px]" title="{{ $row->nomor_faktur }}">
                                No: <strong class="text-indigo-300">{{ $row->nomor_faktur }}</strong>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                Tgl: {{ $row->tanggal_faktur ? \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('d M Y') : '-' }}
                            </div>
                            @if($row->dokumen_faktur || $row->dokumen_kuitansi)
                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    @if($row->dokumen_faktur)
                                        <a href="{{ asset('storage/' . $row->dokumen_faktur) }}" target="_blank"
                                            title="Buka Berkas Nota / Faktur Asli"
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-indigo-500/15 hover:bg-indigo-500/30 text-indigo-300 hover:text-white border border-indigo-500/30 text-[9.5px] font-bold transition-all shadow-sm">
                                            <span>📄</span>
                                            <span>Nota</span>
                                        </a>
                                    @endif
                                    @if($row->dokumen_kuitansi)
                                        <a href="{{ asset('storage/' . $row->dokumen_kuitansi) }}" target="_blank"
                                            title="Buka Berkas Kuitansi / Bukti Penerimaan"
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-cyan-500/15 hover:bg-cyan-500/30 text-cyan-300 hover:text-white border border-cyan-500/30 text-[9.5px] font-bold transition-all shadow-sm">
                                            <span>📦</span>
                                            <span>Kuitansi</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <!-- 3. Rincian Barang (Akun 108) -->
                        <td class="py-3.5 px-3">
                            @if($totalItemCount > 1)
                                <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                    <span class="text-xs font-extrabold text-white group-hover:text-indigo-300 transition-colors">
                                        📦 {{ $totalItemCount }} Macam
                                    </span>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/30">
                                        Multi-Item
                                    </span>
                                    @foreach($categories->take(2) as $catBadge)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-extrabold border leading-none {{ match($catBadge) {
                                            'KIB A' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
                                            'KIB B' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40',
                                            'KIB C' => 'bg-purple-500/20 text-purple-300 border-purple-500/40',
                                            'KIB D' => 'bg-teal-500/20 text-teal-300 border-teal-500/40',
                                            'KIB E' => 'bg-orange-500/20 text-orange-300 border-orange-500/40',
                                            'KIB F' => 'bg-rose-500/20 text-rose-300 border-rose-500/40',
                                            'ATB'   => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40',
                                            'EXTRACOM' => 'bg-amber-400/20 text-amber-300 border-amber-400/40',
                                            default => 'bg-slate-800 text-slate-300 border-slate-700'
                                        } }}">
                                            {{ $catBadge === 'EXTRACOM' ? 'Extracom' : $catBadge }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="text-[11px] text-slate-300 truncate max-w-[220px]" title="{{ $firstAstap?->nama_barang }}">
                                    1. {{ $firstAstap?->nama_barang }} ({{ $firstAstap?->jumlah_volume }} {{ $firstAstap?->satuan }})
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    + {{ $totalItemCount - 1 }} barang lainnya · Total <strong>{{ $totalVolFaktur }} Unit</strong>
                                </div>
                            @elseif($firstAstap)
                                @php
                                    $firstCat = $firstAstap->category ?: 'KIB B';
                                    $firstCatClass = match($firstCat) {
                                        'KIB A' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
                                        'KIB B' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40',
                                        'KIB C' => 'bg-purple-500/20 text-purple-300 border-purple-500/40',
                                        'KIB D' => 'bg-teal-500/20 text-teal-300 border-teal-500/40',
                                        'KIB E' => 'bg-orange-500/20 text-orange-300 border-orange-500/40',
                                        'KIB F' => 'bg-rose-500/20 text-rose-300 border-rose-500/40',
                                        'ATB'   => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40',
                                        'EXTRACOM' => 'bg-amber-400/20 text-amber-300 border-amber-400/40',
                                        default => 'bg-slate-800 text-slate-300 border-slate-700'
                                    };
                                @endphp
                                <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold whitespace-nowrap leading-none shrink-0 border {{ $firstCatClass }}">
                                        {{ $firstCat === 'EXTRACOM' ? 'Extracom' : $firstCat }}
                                    </span>
                                    <span class="text-xs font-bold text-white group-hover:text-indigo-300 transition-colors leading-snug truncate max-w-[180px]" title="{{ $firstAstap->nama_barang }}">
                                        {{ $firstAstap->nama_barang }}
                                    </span>
                                </div>
                                <div class="text-[11px] font-mono text-indigo-400 mt-0.5">
                                    {{ $firstAstap->kode_108 ?: ($firstAstap->jenisAstap?->sub_sub_rincian_objek ?: '-') }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    Vol: <strong>{{ $firstAstap->jumlah_volume }} {{ $firstAstap->satuan }}</strong> · Kondisi: <strong class="text-emerald-400">{{ $firstReg?->kondisi ?: 'Baik' }}</strong>
                                </div>
                            @else
                                <span class="text-slate-500 text-xs italic">Tanpa Rincian Barang</span>
                            @endif
                        </td>

                        <!-- 4. Volume / Kuantitas -->
                        <td class="py-3.5 px-3 text-center whitespace-nowrap w-32">
                            <div class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800 text-teal-300 font-semibold font-mono text-xs whitespace-nowrap">
                                <span>📏</span>
                                <span>{{ $totalVolFaktur }} {{ $astaps->first()?->satuan ?: 'Unit' }}</span>
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5 whitespace-nowrap">
                                {{ $totalItemCount }} macam
                            </div>
                        </td>

                        <!-- 5. Total Pembelian (Rp) -->
                        <td class="py-3.5 px-3 text-right whitespace-nowrap">
                            <div class="text-sm font-extrabold text-emerald-400 font-mono tracking-tight whitespace-nowrap">
                                Rp {{ number_format($row->total_pembelian, 0, ',', '.') }}
                            </div>
                        </td>

                        <!-- 7. Aksi (Detail, Reklas, Ubah, Hapus) -->
                        <td class="py-3 px-2.5 text-center whitespace-nowrap border-l border-slate-800/80 w-[230px] min-w-[230px] shrink-0 sticky-aksi-belanja">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- 1. Tombol Detail -->
                                <button type="button"
                                    @click="openDetailModal({{ json_encode($itemData) }})"
                                    title="Lihat Rincian Faktur Belanja Barang"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 hover:border-emerald-400 font-bold text-xs transition-all duration-200 shadow-sm active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-emerald-400 group-hover/btn:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Detail</span>
                                </button>

                                <!-- 2. Tombol Reklas -->
                                <button type="button"
                                    @click="openReklas({{ $firstAstap?->id ?? 'null' }}, '{{ addslashes($row->nomor_faktur) }}')"
                                    title="Reklasifikasi Aset (Pindah KIB / Ekstrakom / Koreksi)"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 font-bold text-xs transition-all duration-200 shadow-sm active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-indigo-400 group-hover/btn:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                    <span>Reklas</span>
                                </button>

                                <!-- 3. Tombol Ubah -->
                                @if($firstAstap)
                                    <a href="{{ url('/astap/' . $firstAstap->id . '/edit?from=belanja_barang') }}"
                                        title="Ubah Data Aset Belanja Barang"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400 font-bold text-xs transition-all duration-200 shadow-sm active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/btn:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Ubah</span>
                                    </a>
                                @else
                                    <button type="button" disabled
                                        title="Aset Tidak Ditemukan"
                                        class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-slate-800/40 text-slate-500 border border-slate-700/50 font-bold text-xs cursor-not-allowed opacity-60 leading-none">
                                        <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Ubah</span>
                                    </button>
                                @endif

                                <!-- 4. Tombol Hapus (Soft Delete ke Recycle Bin) -->
                                <button type="button"
                                    @click="openConfirmDelete('{{ $row->id }}', '{{ addslashes($row->nomor_faktur) }}')"
                                    title="Pindahkan Faktur ke Recycle Bin"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 hover:border-rose-400 font-bold text-xs transition-all duration-200 shadow-sm active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-rose-400 group-hover/btn:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center">
                            <div class="w-16 h-16 rounded-3xl bg-slate-950 border border-slate-800 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                                📦
                            </div>
                            <h3 class="text-sm font-bold text-white mb-1">Belum Ada Catatan Belanja Barang</h3>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                                Tidak ada data faktur pembelian barang operasional/perbekalan yang cocok dengan filter aktif saat ini.
                            </p>
                            <a href="{{ route('astap.create_belanja_barang') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-lg shadow-indigo-600/20">
                                <span>+ Input Belanja Barang Baru</span>
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

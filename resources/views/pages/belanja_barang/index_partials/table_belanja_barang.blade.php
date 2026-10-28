<!-- ========================================================================= -->
<!-- TABEL DATA MASTER BELANJA BARANG MULTI-ITEM (INSTALASI PERBEKALAN)        -->
<!-- ========================================================================= -->
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
            Inventaris Ekstrakomptabel
        </span>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800 font-extrabold">
                <tr>
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4 min-w-[210px]">Dokumen Faktur &amp; Toko</th>
                    <th class="py-3.5 px-4 min-w-[240px]">Rincian Barang (Akun 108)</th>
                    <th class="py-3.5 px-4 min-w-[180px]">Alokasi Penempatan</th>
                    <th class="py-3.5 px-4 min-w-[140px] text-right">Total Pembelian (Rp)</th>
                    <th class="py-3.5 px-4 min-w-[110px] text-center">Berkas Lampiran</th>
                    <th class="py-3.5 px-4 w-28 text-center">Aksi</th>
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

                        // Data untuk modal detail
                        $itemData = [
                            'id'                   => $row->id,
                            'nomor_faktur'         => $row->nomor_faktur,
                            'tanggal_faktur'       => $row->tanggal_faktur ? \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('d F Y') : '-',
                            'toko_penyedia'        => $row->toko_penyedia,
                            'total_pembelian'      => (float) $row->total_pembelian,
                            'status_alokasi'       => $row->status_alokasi ?: 'gudang',
                            'keterangan'           => $row->keterangan ?: '-',
                            'dokumen_faktur_url'   => $row->dokumen_faktur_url,
                            'dokumen_kuitansi_url' => $row->dokumen_kuitansi_url,
                            'total_item_count'     => $totalItemCount,
                            'total_volume'         => $totalVolFaktur,
                            'tahun'                => $firstAstap?->tahun_perolehan ?: date('Y'),
                            'triwulan'             => $firstAstap?->triwulan ?: 'TW I',
                            'items'                => $astaps->map(function ($a) {
                                return [
                                    'id'            => $a->id,
                                    'nama_barang'   => $a->nama_barang,
                                    'kode_108'      => $a->kode_108 ?: ($a->jenisAstap?->sub_sub_rincian_objek ?: '-'),
                                    'nama_108'      => $a->jenisAstap?->nama ?: 'Aset Ekstrakomptabel',
                                    'jumlah_volume' => (int) $a->jumlah_volume,
                                    'satuan'        => $a->satuan ?: 'Unit',
                                    'harga_satuan'  => (float) $a->harga_satuan,
                                    'subtotal'      => (float) $a->total_realisasi,
                                    'ruangan'       => $a->unit?->nama ?: ($a->registers?->first()?->ruang_pemegang ?: 'Gudang Perbekalan'),
                                    'kondisi'       => $a->registers?->first()?->kondisi ?: 'Baik',
                                    'status'        => $a->registers?->first()?->status ?: 'Tersedia',
                                    'registers'     => $a->registers->map(fn($r) => [
                                        'nibar'   => $r->nibar,
                                        'kondisi' => $r->kondisi ?: 'Baik',
                                        'status'  => $r->status,
                                        'ruang'   => $r->ruang_pemegang ?: ($r->unit?->nama ?: 'Gudang Perbekalan')
                                    ])->values()
                                ];
                            })->values()
                        ];
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors group">
                        <!-- 1. Nomor -->
                        <td class="py-4 px-4 text-center font-mono text-slate-500 text-xs">
                            {{ $idx + 1 }}
                        </td>

                        <!-- 2. Dokumen Faktur & Toko -->
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="text-xs font-bold text-white truncate max-w-[210px]" title="{{ $row->toko_penyedia }}">
                                    🏪 {{ $row->toko_penyedia }}
                                </span>
                            </div>
                            <div class="text-[11px] font-mono text-slate-300 truncate max-w-[210px]" title="{{ $row->nomor_faktur }}">
                                No: <strong class="text-indigo-300">{{ $row->nomor_faktur }}</strong>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                Tgl: {{ $row->tanggal_faktur ? \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('d M Y') : '-' }}
                            </div>
                        </td>

                        <!-- 3. Rincian Barang (Akun 108) -->
                        <td class="py-4 px-4">
                            @if($totalItemCount > 1)
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="text-xs font-extrabold text-white group-hover:text-indigo-300 transition-colors">
                                        📦 {{ $totalItemCount }} Macam Barang
                                    </span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/30">
                                        Multi-Item
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-300 truncate max-w-[240px]" title="{{ $firstAstap?->nama_barang }}">
                                    1. {{ $firstAstap?->nama_barang }} ({{ $firstAstap?->jumlah_volume }} {{ $firstAstap?->satuan }})
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    + {{ $totalItemCount - 1 }} barang lainnya · Total <strong>{{ $totalVolFaktur }} Unit</strong>
                                </div>
                            @elseif($firstAstap)
                                <div class="text-xs font-bold text-white group-hover:text-indigo-300 transition-colors leading-snug">
                                    {{ $firstAstap->nama_barang }}
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

                        <!-- 4. Alokasi Penempatan -->
                        <td class="py-4 px-4">
                            @if($row->status_alokasi === 'ruangan' && $firstAstap?->unit)
                                <div class="inline-flex items-center gap-1 text-xs font-bold text-indigo-300">
                                    <span>🏢</span>
                                    <span class="truncate max-w-[160px]" title="{{ $firstAstap->unit->nama }}">
                                        {{ $firstAstap->unit->nama }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-indigo-400/80 font-medium mt-0.5">
                                    ✓ Aktif di KIR Ruangan
                                </div>
                            @elseif($row->status_alokasi === 'sebagian')
                                <div class="inline-flex items-center gap-1 text-xs font-bold text-amber-300">
                                    <span>🔀</span>
                                    <span>Sebagian Gudang / Ruang</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Alokasi per-item spesifik
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1 text-xs font-bold text-emerald-400">
                                    <span>📦</span>
                                    <span>Gudang Perbekalan</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Buffer Stok · Siap Distribusi
                                </div>
                            @endif
                        </td>

                        <!-- 5. Total Pembelian (Rp) -->
                        <td class="py-4 px-4 text-right">
                            <div class="text-xs font-black text-white font-mono">
                                Rp {{ number_format($row->total_pembelian, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                {{ $totalItemCount }} macam / {{ $totalVolFaktur }} unit
                            </div>
                        </td>

                        <!-- 6. Berkas Lampiran -->
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($row->dokumen_faktur)
                                    <a href="{{ asset('storage/' . $row->dokumen_faktur) }}" target="_blank"
                                        title="Buka Berkas Nota / Faktur Asli"
                                        class="p-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 hover:text-indigo-300 border border-indigo-500/30 text-[10px] font-bold transition-all flex items-center gap-1">
                                        <span>📄</span>
                                        <span>Nota</span>
                                    </a>
                                @else
                                    <span class="text-slate-600 text-[10px]">-</span>
                                @endif

                                @if($row->dokumen_kuitansi)
                                    <a href="{{ asset('storage/' . $row->dokumen_kuitansi) }}" target="_blank"
                                        title="Buka Berkas Kuitansi / Bukti Penerimaan"
                                        class="p-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 hover:text-cyan-300 border border-cyan-500/30 text-[10px] font-bold transition-all flex items-center gap-1">
                                        <span>📦</span>
                                        <span>Kuitansi</span>
                                    </a>
                                @endif
                            </div>
                        </td>

                        <!-- 7. Aksi -->
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Tombol Detail Modal -->
                                <button type="button"
                                    @click="openDetailModal({{ json_encode($itemData) }})"
                                    title="Lihat Rincian Faktur Belanja Barang"
                                    class="p-1.5 rounded-xl bg-slate-800 hover:bg-indigo-600/30 text-slate-300 hover:text-indigo-300 border border-slate-700 hover:border-indigo-500/40 transition-all text-xs font-bold cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                <!-- Tombol Soft Delete -->
                                <button type="button"
                                    @click="openConfirmDelete('{{ $row->id }}', '{{ addslashes($row->nomor_faktur) }}')"
                                    title="Pindahkan Faktur ke Recycle Bin"
                                    class="p-1.5 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-slate-700 hover:border-rose-500/40 transition-all text-xs font-bold cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center">
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

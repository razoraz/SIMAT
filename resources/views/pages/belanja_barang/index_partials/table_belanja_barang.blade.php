<!-- ========================================================================= -->
<!-- TABEL DATA MASTER BELANJA BARANG (AKUN 5.1.02)                             -->
<!-- ========================================================================= -->
<div class="rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl overflow-hidden">
    <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                <span>📋 Daftar Belanja Barang &amp; Perbekalan Ruangan</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Total {{ count($belanjaBarangRecords ?? []) }} data perolehan belanja barang tercatat dalam sistem SIMAT-RK.
            </p>
        </div>

        <span class="text-[11px] font-mono font-bold text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-xl border border-indigo-500/30">
            Akun 5.1.02 Ekstrakomptabel
        </span>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800 font-extrabold">
                <tr>
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4 min-w-[200px]">Dokumen Faktur &amp; Toko</th>
                    <th class="py-3.5 px-4 min-w-[220px]">Identitas Barang (Akun 108)</th>
                    <th class="py-3.5 px-4 min-w-[170px]">Ruangan Penempatan</th>
                    <th class="py-3.5 px-4 min-w-[140px] text-right">Total Pembelian (Rp)</th>
                    <th class="py-3.5 px-4 min-w-[110px] text-center">Klasifikasi</th>
                    <th class="py-3.5 px-4 w-28 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($belanjaBarangRecords ?? [] as $idx => $row)
                    @php
                        $astap = $row->astap;
                        $firstReg = $astap?->registers?->first();
                        $unit = $firstReg?->unit ?? $astap?->unit;
                        $totalRegCount = $astap?->registers?->count() ?? 0;
                        $firstNibar = $firstReg?->nibar ?: '-';
                        $vol = max(1, (int) ($astap?->jumlah_volume ?? 1));
                        $sat = $astap?->satuan ?: 'Unit';
                        $itemData = [
                            'id'              => $row->id,
                            'astap_id'        => $astap?->id,
                            'toko_penyedia'   => $row->toko_penyedia,
                            'nomor_faktur'    => $row->nomor_faktur,
                            'tanggal_faktur'  => $row->tanggal_faktur ? \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('d F Y') : '-',
                            'nama_barang'     => $astap?->nama_barang ?: 'Barang Belanja',
                            'kode_108'        => $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '-'),
                            'nama_108'        => $astap?->jenisAstap?->nama ?: 'Aset Ekstrakomptabel',
                            'jumlah_volume'   => $vol,
                            'satuan'          => $sat,
                            'harga_satuan'    => (float) ($astap?->harga_satuan ?: ($row->total_pembelian / $vol)),
                            'total_pembelian' => (float) $row->total_pembelian,
                            'tahun'           => $astap?->tahun_perolehan ?: date('Y'),
                            'triwulan'        => $astap?->triwulan ?: 'TW I',
                            'ruang_pemegang'  => $unit?->nama ?: ($firstReg?->ruang_pemegang ?: ($astap?->alamat_barang ?: 'RSUD Dr. H. Koesnandi')),
                            'kondisi'         => $firstReg?->kondisi ?: 'Baik',
                            'keterangan'      => $row->keterangan ?: ($astap?->keterangan_tambahan ?: '-'),
                            'registers'       => ($astap?->registers ?? collect())->map(fn($r) => [
                                'nibar' => $r->nibar,
                                'kondisi' => $r->kondisi ?: 'Baik',
                                'status' => $r->status,
                                'ruang' => $r->ruang_pemegang ?: ($unit?->nama ?: '-')
                            ])->values()
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
                                <span class="text-xs font-bold text-white truncate max-w-[200px]" title="{{ $row->toko_penyedia }}">
                                    🏪 {{ $row->toko_penyedia }}
                                </span>
                            </div>
                            <div class="text-[11px] font-mono text-slate-400 truncate max-w-[200px]" title="{{ $row->nomor_faktur }}">
                                No: {{ $row->nomor_faktur }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                Tgl: {{ $row->tanggal_faktur ? \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('d M Y') : '-' }}
                            </div>
                        </td>

                        <!-- 3. Identitas Barang & 108 -->
                        <td class="py-4 px-4">
                            <div class="text-xs font-bold text-white group-hover:text-indigo-300 transition-colors leading-snug">
                                {{ $astap?->nama_barang ?: 'Barang Perbekalan' }}
                            </div>
                            <div class="text-[11px] font-mono text-indigo-400 mt-0.5">
                                {{ $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '5.1.02.x') }}
                            </div>
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400">
                                <span>Vol: <strong>{{ $vol }} {{ $sat }}</strong></span>
                                <span>•</span>
                                <span>Kondisi: <strong class="text-emerald-400">{{ $firstReg?->kondisi ?: 'Baik' }}</strong></span>
                            </div>
                        </td>

                        <!-- 4. Ruangan Penempatan -->
                        <td class="py-4 px-4">
                            <div class="text-xs font-semibold text-slate-200">
                                🏢 {{ $unit?->nama ?: ($firstReg?->ruang_pemegang ?: 'RSUD Dr. H. Koesnandi') }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                NIBAR: <span class="font-mono text-slate-300">{{ $firstNibar }}</span>
                            </div>
                            @if($totalRegCount > 1)
                                <div class="text-[9px] text-indigo-400/90 font-medium mt-0.5">
                                    +{{ $totalRegCount - 1 }} unit fisik lainnya
                                </div>
                            @endif
                        </td>

                        <!-- 5. Total Pembelian (Rp) -->
                        <td class="py-4 px-4 text-right">
                            <div class="text-xs font-black text-white font-mono">
                                Rp {{ number_format($row->total_pembelian, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                @ Rp {{ number_format($vol > 0 ? $row->total_pembelian / $vol : 0, 0, ',', '.') }}
                            </div>
                        </td>

                        <!-- 6. Klasifikasi -->
                        <td class="py-4 px-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                Ekstrakomptabel
                            </span>
                            <div class="text-[9px] text-slate-500 mt-1">
                                {{ $astap?->triwulan ?: 'TW I' }} · {{ $astap?->tahun_perolehan ?: date('Y') }}
                            </div>
                        </td>

                        <!-- 7. Aksi -->
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Tombol Detail -->
                                <button type="button"
                                    @click='openDetailModal(@json($itemData))'
                                    class="p-2 rounded-xl bg-slate-950 border border-slate-800 hover:border-indigo-500/50 text-indigo-400 hover:text-indigo-300 transition-all hover:scale-105"
                                    title="Lihat Detail Barang & NIBAR">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                <!-- Tombol Hapus -->
                                <button type="button"
                                    @click="openConfirmDelete({{ $row->id }}, '{{ addslashes($astap?->nama_barang ?: 'Barang Belanja') }}')"
                                    class="p-2 rounded-xl bg-slate-950 border border-slate-800 hover:border-rose-500/50 text-rose-400 hover:text-rose-300 transition-all hover:scale-105"
                                    title="Hapus / Batalkan Data Belanja Barang">
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
                            <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-slate-800 text-slate-600 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                                📦
                            </div>
                            <p class="text-sm font-bold text-white">Belum ada data Belanja Barang</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                Belum ada aset perolehan belanja barang (akun 5.1.02) yang dicatat atau sesuai dengan filter pencarian.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('astap.create_belanja_barang') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-lg shadow-indigo-600/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Catat Belanja Barang Baru</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

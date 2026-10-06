{{-- TABEL 1: SEMUA KOREKSI NILAI (KOMPREHENSIF / MASTER LEDGER) --}}
<div class="rounded-3xl bg-slate-900/90 border border-slate-800 overflow-hidden shadow-2xl backdrop-blur-xl">
    <div class="p-5 border-b border-slate-800 flex items-center justify-between flex-wrap gap-3 bg-slate-950/40">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-sm">
                📑
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">Buku Besar Audit Koreksi Nilai BMD (Semua Kategori)</h3>
                <p class="text-[11px] text-slate-400">Jejak lengkap penyesuaian nilai buku aset tetap RSUD dr. H. Koesnandi</p>
            </div>
        </div>
        <div class="text-xs font-mono text-slate-400">
            Total Transaksi: <strong class="text-white">{{ $semuaKoreksi->count() }}</strong>
        </div>
    </div>

    @if ($semuaKoreksi->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-center mx-auto text-2xl">
                🔍
            </div>
            <div class="text-white font-bold text-sm">Tidak Ditemukan Data Koreksi Nilai</div>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Belum ada transaksi koreksi nilai (Biasa, LKD, atau Manset) untuk filter periode T.A. {{ $selectedYear }} yang dipilih.
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/70 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Waktu &amp; Periode</th>
                        <th class="py-3 px-4">Sub-Koreksi</th>
                        <th class="py-3 px-4">Identitas Aset Tetap</th>
                        <th class="py-3 px-4 text-right">Nilai Koreksi</th>
                        <th class="py-3 px-4">Komparasi Nilai Buku</th>
                        <th class="py-3 px-4">Dokumen Dasar / Uraian</th>
                        <th class="py-3 px-4 text-center">Dampak RMB</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach ($semuaKoreksi as $idx => $item)
                        @php
                            $sub = $item->sub_koreksi ?? 'biasa';
                            $tipe = $item->tipe_koreksi;
                            $dampak = $item->dampak_rmb;
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors group">
                            {{-- 1. No --}}
                            <td class="py-3 px-4 text-center font-mono text-slate-500 text-[11px]">
                                {{ $idx + 1 }}
                            </td>

                            {{-- 2. Waktu & Periode --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-semibold text-white">
                                    {{ $item->tanggal_reklas?->format('d/m/Y') ?: '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <span class="px-1.5 py-0.2 rounded bg-slate-800 border border-slate-700 text-slate-300 font-mono">
                                        TW {{ $item->triwulan }}
                                    </span>
                                    <span>T.A. {{ $item->tahun }}</span>
                                </div>
                            </td>

                            {{-- 3. Sub-Koreksi Badge --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if ($sub === 'lkd')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-cyan-950/60 border border-cyan-500/40 text-cyan-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                        Koreksi LKD (BPK)
                                    </span>
                                @elseif ($sub === 'manset')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-950/60 border border-emerald-500/40 text-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Koreksi Manset (BPKAD)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-indigo-950/60 border border-indigo-500/40 text-indigo-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                        Koreksi Biasa (Internal)
                                    </span>
                                @endif
                            </td>

                            {{-- 4. Identitas Aset Tetap --}}
                            <td class="py-3 px-4 min-w-[220px]">
                                <div class="font-bold text-white group-hover:text-emerald-300 transition-colors line-clamp-1">
                                    {{ $item->astap?->nama_barang ?: ($item->asal_nama ?: 'Aset Tidak Ditemukan') }}
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-[10px] font-mono">
                                    <span class="text-amber-400/90 font-semibold" title="Nomor Induk Barang (NIBAR)">
                                        {{ $item->astap?->nibar ?: '-' }}
                                    </span>
                                    <span class="text-slate-500">·</span>
                                    <span class="px-1.5 py-0.2 rounded bg-slate-800 text-slate-300">
                                        {{ $item->astap?->category ?: ($item->asal_kib ?: 'KIB') }}
                                    </span>
                                </div>
                            </td>

                            {{-- 5. Arah & Nominal Koreksi --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $tipe === 'tambah' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                        {{ $tipe === 'tambah' ? '+ TAMBAH' : '- KURANG' }}
                                    </span>
                                </div>
                                <div class="text-sm font-black font-mono mt-1 {{ $tipe === 'tambah' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tipe === 'tambah' ? '+' : '-' }} Rp {{ number_format((float) $item->nilai_reklas, 2, ',', '.') }}
                                </div>
                            </td>

                            {{-- 6. Komparasi Nilai Buku --}}
                            <td class="py-3 px-4 whitespace-nowrap text-[11px] font-mono">
                                <div class="text-slate-400 text-[10px]">
                                    Semula: <span class="text-slate-300">Rp {{ number_format($item->nilai_semula, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-white font-semibold mt-0.5">
                                    Baru: <span class="text-emerald-300">Rp {{ number_format($item->nilai_setelah_koreksi, 0, ',', '.') }}</span>
                                </div>
                            </td>

                            {{-- 7. Dokumen Dasar & Uraian --}}
                            <td class="py-3 px-4 min-w-[200px]">
                                <div class="font-semibold text-slate-200 line-clamp-1" title="{{ $item->nomor_ba_reklas ?: '-' }}">
                                    📄 {{ $item->nomor_ba_reklas ?: '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5" title="{{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}">
                                    {{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}
                                </div>
                            </td>

                            {{-- 8. Dampak RMB --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-block px-2 py-1 rounded-xl text-[10px] font-mono font-bold bg-slate-950 border border-slate-800 text-slate-300"
                                      title="{{ $dampak['label'] }}">
                                    {{ $dampak['kolom_text'] }} ({{ $dampak['arah'] }})
                                </span>
                            </td>

                            {{-- 9. Aksi --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <button type="button" @click="openDetailModal({{ $item->id }})"
                                        class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 hover:text-white font-bold text-[11px] transition-all flex items-center gap-1.5 mx-auto">
                                    <span>🔍</span>
                                    <span>Rincian</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

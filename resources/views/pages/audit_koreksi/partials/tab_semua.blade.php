{{-- TABEL 1: SEMUA KOREKSI NILAI (KOMPREHENSIF / MASTER LEDGER) --}}
<style>
    /* Layar Medium ke Bawah (< 1024px): Center Card Header & Non-Sticky Aksi + Swipe Hint */
    @media (max-width: 1023px) {
        .swipe-hint-audit {
            display: flex;
        }
        .sticky-aksi-koreksi {
            position: static !important;
            right: auto !important;
            z-index: auto !important;
            background-color: transparent !important;
            box-shadow: none !important;
        }
        th.sticky-aksi-koreksi {
            background-color: #020617 !important;
        }
        .audit-card-header {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
        }
        .audit-card-title-group {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
        }
        .audit-card-heading {
            justify-content: center !important;
        }
    }
    /* Desktop Luas (>= 1024px): Freeze Column Aksi Koreksi Nilai BMD (Anti-Bocor saat Scroll Horizontal) */
    @media (min-width: 1024px) {
        .swipe-hint-audit {
            display: none !important;
        }
        .sticky-aksi-koreksi {
            position: sticky !important;
            right: 0 !important;
            z-index: 10 !important;
            background-color: #0f172a !important; /* Solid Slate-900 Opaque */
            box-shadow: -6px 0 12px rgba(0, 0, 0, 0.6) !important;
        }
        tr:hover .sticky-aksi-koreksi {
            background-color: #1e293b !important; /* Slate-800 saat hover */
        }
        th.sticky-aksi-koreksi {
            position: sticky !important;
            top: 0 !important;
            right: 0 !important;
            z-index: 20 !important;
            background-color: #020617 !important; /* Solid Slate-950 Opaque */
        }
        .audit-card-header {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            text-align: left !important;
        }
        .audit-card-title-group {
            flex-direction: row !important;
            align-items: center !important;
            text-align: left !important;
        }
        .audit-card-heading {
            justify-content: flex-start !important;
        }
    }
</style>

<div class="rounded-3xl bg-slate-900/90 border border-slate-800 overflow-hidden shadow-xl backdrop-blur-xl">
    {{-- Card Header --}}
    <div class="audit-card-header p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between flex-wrap gap-3 bg-slate-950/60">
        <div class="audit-card-title-group flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl bg-slate-800/90 border border-slate-700/80 flex items-center justify-center text-slate-300 shadow-sm shrink-0">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="audit-card-heading text-sm font-bold text-white flex flex-wrap items-center gap-2">
                    <span>Buku Besar Audit Koreksi Nilai BMD (Semua Kategori)</span>
                </h3>
                <p class="text-[11px] text-slate-400">Jejak komprehensif penyesuaian nilai buku aset tetap RSUD dr. H. Koesnandi</p>
            </div>
        </div>
        <div class="text-xs font-mono text-slate-400 flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-slate-800 shrink-0">
            <span>Total Transaksi:</span>
            <strong class="text-emerald-400">{{ $semuaKoreksi->count() }}</strong>
        </div>
    </div>

    @if ($semuaKoreksi->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-center mx-auto text-slate-400 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div class="text-white font-bold text-sm">Tidak Ditemukan Data Koreksi Nilai</div>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Belum ada transaksi koreksi nilai (Biasa, LKD, atau Manset) untuk filter periode T.A. {{ $selectedYear }} yang dipilih.
            </p>
        </div>
    @else
        {{-- Indikator Geser Tabel pada Layar Mobile/Medium (< 1024px) --}}
        <div class="swipe-hint-audit items-center justify-between px-4 py-2 text-[10.5px] text-slate-400 border-t border-slate-800/80 bg-slate-950/30">
            <span class="flex items-center gap-1.5">
                <span class="text-emerald-400">👉</span>
                <span>Geser tabel ke kanan untuk melihat detail &amp; kolom aksi</span>
            </span>
            <span class="font-mono text-slate-500">Tabel Responsif</span>
        </div>

        <div class="rounded-2xl border-t border-slate-800/80 bg-slate-950/40 custom-scrollbar" style="max-height: calc(100vh - 280px); overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-w-[1050px]">
                <thead class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800 shrink-0 select-none" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                    <tr>
                        <th class="py-3 px-3 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                        <th class="py-3 px-3.5 whitespace-nowrap bg-slate-950">Waktu &amp; Periode</th>
                        <th class="py-3 px-3.5 whitespace-nowrap bg-slate-950">Sub-Koreksi</th>
                        <th class="py-3 px-4 min-w-[240px] bg-slate-950">Identitas Aset Tetap</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Nilai Koreksi</th>
                        <th class="py-3 px-4 whitespace-nowrap bg-slate-950">Komparasi Nilai Buku</th>
                        <th class="py-3 px-3.5 text-center whitespace-nowrap w-28 min-w-[110px] border-l border-slate-800 shrink-0 sticky-aksi-koreksi">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @foreach ($semuaKoreksi as $idx => $item)
                        @php
                            $sub = $item->sub_koreksi ?? 'biasa';
                            $tipe = $item->tipe_koreksi;
                            $dampak = $item->dampak_rmb;
                            $cat = strtoupper($item->astap?->category ?: ($item->asal_kib ?: 'KIB B'));
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors group">
                            {{-- 1. No --}}
                            <td class="py-3.5 px-3 text-center font-mono font-bold text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $idx + 1 }}
                            </td>

                            {{-- 2. Waktu & Periode --}}
                            <td class="py-3.5 px-3.5 whitespace-nowrap">
                                <div class="font-bold text-white text-xs">
                                    {{ $item->tanggal_reklas?->format('d/m/Y') ?: '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-300 font-mono font-bold">
                                        TW {{ $item->triwulan }}
                                    </span>
                                    <span>T.A. {{ $item->tahun }}</span>
                                </div>
                            </td>

                            {{-- 3. Sub-Koreksi Badge --}}
                            <td class="py-3.5 px-3.5 whitespace-nowrap">
                                @if ($sub === 'lkd')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10.5px] font-bold bg-cyan-950/70 border border-cyan-500/40 text-cyan-300 shadow-sm shadow-cyan-950/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                        Koreksi LKD (BPK)
                                    </span>
                                @elseif ($sub === 'manset')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10.5px] font-bold bg-emerald-950/70 border border-emerald-500/40 text-emerald-300 shadow-sm shadow-emerald-950/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Koreksi Manset (BPKAD)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10.5px] font-bold bg-indigo-950/70 border border-indigo-500/40 text-indigo-300 shadow-sm shadow-indigo-950/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                                        Koreksi Biasa (Internal)
                                    </span>
                                @endif
                            </td>

                            {{-- 4. Identitas Aset Tetap --}}
                            <td class="py-3.5 px-4 min-w-[240px]">
                                <div class="font-bold text-white text-xs group-hover:text-emerald-300 transition-colors line-clamp-1">
                                    {{ $item->astap?->nama_barang ?: ($item->asal_nama ?: 'Aset Tidak Ditemukan') }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 text-[10px] font-mono flex-wrap">
                                    {{-- KIB Color Canonical Badge --}}
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-extrabold whitespace-nowrap leading-none
                                        {{ $cat === 'KIB A' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : '' }}
                                        {{ $cat === 'KIB B' ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40' : '' }}
                                        {{ $cat === 'KIB C' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40' : '' }}
                                        {{ $cat === 'KIB D' ? 'bg-teal-500/20 text-teal-300 border border-teal-500/40' : '' }}
                                        {{ $cat === 'KIB E' ? 'bg-orange-500/20 text-orange-300 border border-orange-500/40' : '' }}
                                        {{ $cat === 'KIB F' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : '' }}
                                        {{ in_array($cat, ['ATB', 'EXTRACOM', 'ASET LAIN']) ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40' : '' }}">
                                        {{ $cat }}
                                    </span>
                                    @if (!empty($item->astap?->nibar))
                                        <span class="text-amber-400 font-bold select-all break-all tracking-tight" title="Nomor Induk Barang (NIBAR)">
                                            {{ $item->astap->nibar }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 5. Arah & Nominal Koreksi --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <span class="px-1.5 py-0.5 rounded text-[9.5px] font-bold uppercase tracking-wider {{ $tipe === 'tambah' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                        {{ $tipe === 'tambah' ? '+ TAMBAH' : '- KURANG' }}
                                    </span>
                                </div>
                                <div class="text-xs sm:text-sm font-black font-mono mt-1 {{ $tipe === 'tambah' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tipe === 'tambah' ? '+' : '-' }}Rp {{ number_format((float) $item->nilai_reklas, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- 6. Komparasi Nilai Buku --}}
                            <td class="py-3.5 px-4 whitespace-nowrap text-[11px] font-mono">
                                <div class="text-slate-400 text-[10px]">
                                    Semula: <span class="text-slate-300 font-semibold">Rp {{ number_format($item->nilai_semula, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-white font-bold mt-0.5">
                                    Pasca: <span class="text-emerald-300">Rp {{ number_format($item->nilai_setelah_koreksi, 0, ',', '.') }}</span>
                                </div>
                            </td>

                            {{-- 7. Aksi (Sticky Freeze Column) --}}
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap sticky-aksi-koreksi">
                                <button type="button" @click="openDetailModal({{ $item->id }})"
                                        class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 active:bg-slate-900 border border-slate-700 text-slate-200 hover:text-white font-bold text-[11px] transition-all flex items-center gap-1.5 mx-auto hover:-translate-y-0.5 active:scale-95 cursor-pointer shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
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
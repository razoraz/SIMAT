{{-- TABEL 3: KHUSUS KOREKSI LKD (AUDIT TEMUAN BPK RI / LKPD) --}}
<div class="rounded-3xl bg-slate-900/90 border border-cyan-500/30 overflow-hidden shadow-2xl shadow-cyan-950/20 backdrop-blur-xl">
    {{-- Card Header --}}
    <div class="p-5 border-b border-cyan-900/40 flex items-center justify-between flex-wrap gap-3 bg-gradient-to-r from-cyan-950/40 to-slate-950/60">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-cyan-900/50 border border-cyan-500/40 flex items-center justify-center text-cyan-300 shadow-sm">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>Audit Koreksi Nilai LKD (Temuan Auditor BPK RI)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                        Kolom 6 &amp; 16
                    </span>
                </h3>
                <p class="text-[11px] text-cyan-200/70">
                    Penyesuaian nilai buku aset tetap berdasarkan Laporan Hasil Pemeriksaan (LHP) BPK RI atas Laporan Keuangan Daerah (LKPD).
                </p>
            </div>
        </div>
        <div class="text-xs font-mono text-cyan-300 flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-cyan-900/40">
            <span>Total Temuan:</span>
            <strong class="text-white">{{ $lkdKoreksi->count() }}</strong>
        </div>
    </div>

    @if ($lkdKoreksi->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-cyan-950/40 border border-cyan-800/40 flex items-center justify-center mx-auto text-cyan-400 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                </svg>
            </div>
            <div class="text-white font-bold text-sm">Tidak Ditemukan Koreksi Nilai LKD</div>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Nihil temuan audit LHP BPK RI yang memerlukan penyesuaian nilai buku pada periode T.A. {{ $selectedYear }}.
            </p>
        </div>
    @else
        <div class="rounded-2xl border-t border-cyan-900/40 bg-slate-950/40 custom-scrollbar" style="max-height: calc(100vh - 280px); overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-w-[1050px]">
                <thead class="text-cyan-300/80 font-extrabold uppercase text-[10px] tracking-wider border-b border-cyan-900/40 shrink-0 select-none" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                    <tr>
                        <th class="py-3 px-3 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                        <th class="py-3 px-3.5 whitespace-nowrap bg-slate-950">Tanggal &amp; TW</th>
                        <th class="py-3 px-4 min-w-[190px] bg-slate-950">Nomor LHP / Temuan BPK</th>
                        <th class="py-3 px-4 min-w-[220px] bg-slate-950">Aset Terperiksa &amp; NIBAR</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Nilai Audited Awal</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Koreksi Nilai BPK</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Nilai Audited Akhir</th>
                        <th class="py-3 px-4 min-w-[200px] bg-slate-950">Rekomendasi LHP BPK RI</th>
                        <th class="py-3 px-3.5 text-center whitespace-nowrap w-28 min-w-[110px] border-l border-slate-800 shrink-0 sticky-aksi-koreksi">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach ($lkdKoreksi as $idx => $item)
                        @php
                            $tipe = $item->tipe_koreksi;
                            $dampak = $item->dampak_rmb;
                            $cat = strtoupper($item->astap?->category ?: ($item->asal_kib ?: 'KIB B'));
                        @endphp
                        <tr class="hover:bg-cyan-950/20 transition-colors group">
                            {{-- 1. No --}}
                            <td class="py-3.5 px-3 text-center font-mono font-bold text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $idx + 1 }}
                            </td>

                            {{-- 2. Tanggal & TW --}}
                            <td class="py-3.5 px-3.5 whitespace-nowrap">
                                <div class="font-bold text-white text-xs">
                                    {{ $item->tanggal_reklas?->format('d/m/Y') ?: '-' }}
                                </div>
                                <div class="text-[10px] text-cyan-300 font-mono mt-0.5">
                                    TW {{ $item->triwulan }} · {{ $item->tahun }}
                                </div>
                            </td>

                            {{-- 3. Nomor LHP / Temuan BPK --}}
                            <td class="py-3.5 px-4 min-w-[190px]">
                                <div class="font-bold text-cyan-300 flex items-center gap-1.5 line-clamp-1" title="{{ $item->nomor_ba_reklas ?: '-' }}">
                                    <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>{{ $item->nomor_ba_reklas ?: 'LHP BPK RI' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Audit Trail Kepatuhan BPK
                                </div>
                            </td>

                            {{-- 4. Aset Terperiksa & NIBAR --}}
                            <td class="py-3.5 px-4 min-w-[220px]">
                                <div class="font-bold text-white text-xs group-hover:text-cyan-300 transition-colors line-clamp-1">
                                    {{ $item->astap?->nama_barang ?: ($item->asal_nama ?: 'Aset Terperiksa') }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 text-[10px] font-mono flex-wrap">
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
                                        <span class="text-amber-400 font-bold select-all break-all tracking-tight" title="Nomor Induk Barang (NIBAR)">{{ $item->astap->nibar }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- 5. Nilai Audited Awal --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono text-[11px] text-slate-300 font-semibold">
                                Rp {{ number_format($item->nilai_semula, 0, ',', '.') }}
                            </td>

                            {{-- 6. Koreksi Nilai BPK --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono">
                                <div class="text-xs sm:text-sm font-black {{ $tipe === 'tambah' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tipe === 'tambah' ? '+' : '-' }}Rp {{ number_format((float) $item->nilai_reklas, 0, ',', '.') }}
                                </div>
                                <div class="text-[9px] uppercase font-bold {{ $tipe === 'tambah' ? 'text-emerald-500/80' : 'text-rose-500/80' }}">
                                    {{ $tipe === 'tambah' ? 'Koreksi Tambah BPK' : 'Koreksi Kurang BPK' }}
                                </div>
                            </td>

                            {{-- 7. Nilai Audited Akhir --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono text-[11px] font-bold text-emerald-300">
                                Rp {{ number_format($item->nilai_setelah_koreksi, 0, ',', '.') }}
                            </td>

                            {{-- 8. Uraian Rekomendasi LHP BPK RI --}}
                            <td class="py-3.5 px-4 min-w-[200px]">
                                <div class="text-slate-300 line-clamp-2 leading-snug text-xs" title="{{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}">
                                    {{ $item->alasan_reklas ?: ($item->keterangan ?: 'Rekomendasi audit LKPD BPK RI.') }}
                                </div>
                            </td>

                            {{-- 9. Aksi (Sticky Freeze Column) --}}
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap sticky-aksi-koreksi">
                                <button type="button" @click="openDetailModal({{ $item->id }})"
                                        class="px-3 py-1.5 rounded-xl bg-cyan-900/40 hover:bg-cyan-800/60 active:bg-cyan-950 border border-cyan-700/50 text-cyan-200 hover:text-white font-bold text-[11px] transition-all flex items-center gap-1.5 mx-auto hover:-translate-y-0.5 active:scale-95 cursor-pointer shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
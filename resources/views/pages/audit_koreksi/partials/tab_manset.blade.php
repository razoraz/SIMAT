{{-- TABEL 4: KHUSUS KOREKSI MANSET (MANAJEMEN ASET / E-MANSET BPKAD) --}}
<div class="rounded-3xl bg-slate-900/90 border border-emerald-500/30 overflow-hidden shadow-2xl shadow-emerald-950/20 backdrop-blur-xl">
    {{-- Card Header --}}
    <div class="p-5 border-b border-emerald-900/40 flex items-center justify-between flex-wrap gap-3 bg-gradient-to-r from-emerald-950/40 to-slate-950/60">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-900/50 border border-emerald-500/40 flex items-center justify-center text-emerald-300 shadow-sm">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>Audit Koreksi Nilai Manset (Sinkronisasi BPKAD Bondowoso)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Kolom 7 &amp; 17 RMB
                    </span>
                </h3>
                <p class="text-[11px] text-emerald-200/70">
                    Penyelarasan nilai kapitalisasi dan kodefikasi antara aplikasi SIMAT-RK RSUD dengan database E-Manset / SIMDA BMD BPKAD.
                </p>
            </div>
        </div>
        <div class="text-xs font-mono text-emerald-300 flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-emerald-900/40">
            <span>Total Sinkronisasi:</span>
            <strong class="text-white">{{ $mansetKoreksi->count() }}</strong>
        </div>
    </div>

    @if ($mansetKoreksi->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-emerald-950/40 border border-emerald-800/40 flex items-center justify-center mx-auto text-emerald-400 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div class="text-white font-bold text-sm">Tidak Ditemukan Koreksi Nilai Manset</div>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Belum ada transaksi penyesuaian sinkronisasi E-Manset BPKAD pada periode T.A. {{ $selectedYear }}.
            </p>
        </div>
    @else
        <div class="rounded-2xl border-t border-emerald-900/40 bg-slate-950/40 custom-scrollbar" style="max-height: calc(100vh - 280px); overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-w-[1050px]">
                <thead class="text-emerald-300/80 font-extrabold uppercase text-[10px] tracking-wider border-b border-emerald-900/40 shrink-0 select-none" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                    <tr>
                        <th class="py-3 px-3 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                        <th class="py-3 px-3.5 whitespace-nowrap bg-slate-950">Tanggal &amp; TW</th>
                        <th class="py-3 px-4 min-w-[190px] bg-slate-950">Nomor BA BPKAD / Manset</th>
                        <th class="py-3 px-4 min-w-[220px] bg-slate-950">Aset &amp; Kodefikasi 108</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Nilai SIMAT RSUD</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Koreksi Penyelarasan</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Nilai Akhir E-Manset</th>
                        <th class="py-3 px-4 min-w-[200px] bg-slate-950">Uraian Harmonisasi</th>
                        <th class="py-3 px-3.5 text-center whitespace-nowrap bg-slate-950">Dampak RMB</th>
                        <th class="py-3 px-3.5 text-center whitespace-nowrap w-28 min-w-[110px] border-l border-slate-800 shrink-0 sticky-aksi-koreksi">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach ($mansetKoreksi as $idx => $item)
                        @php
                            $tipe = $item->tipe_koreksi;
                            $dampak = $item->dampak_rmb;
                            $cat = strtoupper($item->astap?->category ?: ($item->asal_kib ?: 'KIB B'));
                        @endphp
                        <tr class="hover:bg-emerald-950/20 transition-colors group">
                            {{-- 1. No --}}
                            <td class="py-3.5 px-3 text-center font-mono font-bold text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $idx + 1 }}
                            </td>

                            {{-- 2. Tanggal & TW --}}
                            <td class="py-3.5 px-3.5 whitespace-nowrap">
                                <div class="font-bold text-white text-xs">
                                    {{ $item->tanggal_reklas?->format('d/m/Y') ?: '-' }}
                                </div>
                                <div class="text-[10px] text-emerald-300 font-mono mt-0.5">
                                    TW {{ $item->triwulan }} · {{ $item->tahun }}
                                </div>
                            </td>

                            {{-- 3. Nomor BA BPKAD / Manset --}}
                            <td class="py-3.5 px-4 min-w-[190px]">
                                <div class="font-bold text-emerald-300 flex items-center gap-1.5 line-clamp-1" title="{{ $item->nomor_ba_reklas ?: '-' }}">
                                    <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>{{ $item->nomor_ba_reklas ?: 'BA BPKAD' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Bidang Pengelolaan BMD
                                </div>
                            </td>

                            {{-- 4. Aset & Kodefikasi 108 --}}
                            <td class="py-3.5 px-4 min-w-[220px]">
                                <div class="font-bold text-white text-xs group-hover:text-emerald-300 transition-colors line-clamp-1">
                                    {{ $item->astap?->nama_barang ?: ($item->asal_nama ?: 'Aset') }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 text-[10px] font-mono flex-wrap">
                                    <span class="text-amber-400 font-bold">{{ $item->astap?->nibar ?: '-' }}</span>
                                    <span class="text-slate-600">·</span>
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
                                </div>
                            </td>

                            {{-- 5. Nilai SIMAT RSUD --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono text-[11px] text-slate-300 font-semibold">
                                Rp {{ number_format($item->nilai_semula, 0, ',', '.') }}
                            </td>

                            {{-- 6. Koreksi Penyelarasan --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono">
                                <div class="text-xs sm:text-sm font-black {{ $tipe === 'tambah' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tipe === 'tambah' ? '+' : '-' }}Rp {{ number_format((float) $item->nilai_reklas, 0, ',', '.') }}
                                </div>
                                <div class="text-[9px] uppercase font-bold {{ $tipe === 'tambah' ? 'text-emerald-500/80' : 'text-rose-500/80' }}">
                                    {{ $tipe === 'tambah' ? 'Harmonisasi Tambah' : 'Harmonisasi Kurang' }}
                                </div>
                            </td>

                            {{-- 7. Nilai Akhir E-Manset --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono text-[11px] font-bold text-emerald-300">
                                Rp {{ number_format($item->nilai_setelah_koreksi, 0, ',', '.') }}
                            </td>

                            {{-- 8. Uraian Harmonisasi BPKAD --}}
                            <td class="py-3.5 px-4 min-w-[200px]">
                                <div class="text-slate-300 line-clamp-2 leading-snug text-xs" title="{{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}">
                                    {{ $item->alasan_reklas ?: ($item->keterangan ?: 'Harmonisasi register dengan SIMDA/E-Manset.') }}
                                </div>
                            </td>

                            {{-- 9. Dampak Kolom RMB --}}
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                @if ($tipe === 'tambah')
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm"
                                          title="Masuk ke Kolom 7 Kertas Kerja RMB BPKAD (Koreksi Manset Bertambah)">
                                        Kolom 7 (+)
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-sm"
                                          title="Masuk ke Kolom 17 Kertas Kerja RMB BPKAD (Koreksi Manset Berkurang)">
                                        Kolom 17 (-)
                                    </span>
                                @endif
                            </td>

                            {{-- 10. Aksi (Sticky Freeze Column) --}}
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap sticky-aksi-koreksi">
                                <button type="button" @click="openDetailModal({{ $item->id }})"
                                        class="px-3 py-1.5 rounded-xl bg-emerald-900/40 hover:bg-emerald-800/60 active:bg-emerald-950 border border-emerald-700/50 text-emerald-200 hover:text-white font-bold text-[11px] transition-all flex items-center gap-1.5 mx-auto hover:-translate-y-0.5 active:scale-95 cursor-pointer shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
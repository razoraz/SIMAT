{{-- TABEL 2: KHUSUS KOREKSI BIASA (INTERNAL RSUD DR. H. KOESNANDI) --}}
<div class="rounded-3xl bg-slate-900/90 border border-indigo-500/30 overflow-hidden shadow-2xl shadow-indigo-950/20 backdrop-blur-xl">
    {{-- Card Header --}}
    <div class="p-5 border-b border-indigo-900/40 flex items-center justify-between flex-wrap gap-3 bg-gradient-to-r from-indigo-950/40 to-slate-950/60">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-900/50 border border-indigo-500/40 flex items-center justify-center text-indigo-400 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>Audit Koreksi Nilai Biasa (Rekonsiliasi Internal RSUD)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        Kolom 5 &amp; 15 RMB
                    </span>
                </h3>
                <p class="text-[11px] text-indigo-200/70">
                    Penyesuaian atas selisih pembukuan internal kas bendahara, kapitalisasi susulan, atau pengembalian belanja ke kas daerah.
                </p>
            </div>
        </div>
        <div class="text-xs font-mono text-indigo-300 flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-indigo-900/40">
            <span>Total Transaksi:</span>
            <strong class="text-white">{{ $biasaKoreksi->count() }}</strong>
        </div>
    </div>

    @if ($biasaKoreksi->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-indigo-950/40 border border-indigo-800/40 flex items-center justify-center mx-auto text-indigo-400 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="text-white font-bold text-sm">Tidak Ditemukan Koreksi Nilai Biasa</div>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Belum ada transaksi koreksi biasa (rekonsiliasi internal) pada periode T.A. {{ $selectedYear }}.
            </p>
        </div>
    @else
        <div class="rounded-2xl border-t border-indigo-900/40 bg-slate-950/40 custom-scrollbar" style="max-height: calc(100vh - 280px); overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-w-[1050px]">
                <thead class="text-indigo-300/80 font-extrabold uppercase text-[10px] tracking-wider border-b border-indigo-900/40 shrink-0 select-none" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                    <tr>
                        <th class="py-3 px-3 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                        <th class="py-3 px-3.5 whitespace-nowrap bg-slate-950">Tanggal &amp; TW</th>
                        <th class="py-3 px-4 min-w-[220px] bg-slate-950">Aset Tetap &amp; NIBAR</th>
                        <th class="py-3 px-4 min-w-[180px] bg-slate-950">Rekening Belanja / SP2D</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Nilai Semula</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Koreksi Internal</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap bg-slate-950">Nilai Pasca Koreksi</th>
                        <th class="py-3 px-4 min-w-[190px] bg-slate-950">BA Rekon Internal</th>
                        <th class="py-3 px-3.5 text-center whitespace-nowrap bg-slate-950">Dampak RMB</th>
                        <th class="py-3 px-3.5 text-center whitespace-nowrap w-28 min-w-[110px] border-l border-slate-800 shrink-0 sticky-aksi-koreksi">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach ($biasaKoreksi as $idx => $item)
                        @php
                            $tipe = $item->tipe_koreksi;
                            $dampak = $item->dampak_rmb;
                            $cat = strtoupper($item->astap?->category ?: ($item->asal_kib ?: 'KIB B'));
                        @endphp
                        <tr class="hover:bg-indigo-950/20 transition-colors group">
                            {{-- 1. No --}}
                            <td class="py-3.5 px-3 text-center font-mono font-bold text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $idx + 1 }}
                            </td>

                            {{-- 2. Tanggal & TW --}}
                            <td class="py-3.5 px-3.5 whitespace-nowrap">
                                <div class="font-bold text-white text-xs">
                                    {{ $item->tanggal_reklas?->format('d/m/Y') ?: '-' }}
                                </div>
                                <div class="text-[10px] text-indigo-300 font-mono mt-0.5">
                                    TW {{ $item->triwulan }} · {{ $item->tahun }}
                                </div>
                            </td>

                            {{-- 3. Aset Tetap & NIBAR --}}
                            <td class="py-3.5 px-4 min-w-[220px]">
                                <div class="font-bold text-white text-xs group-hover:text-indigo-300 transition-colors line-clamp-1">
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

                            {{-- 4. Rekening Belanja / SP2D --}}
                            <td class="py-3.5 px-4 min-w-[180px]">
                                <div class="text-[11px] font-mono text-slate-300 line-clamp-1" title="{{ $item->astap?->rekeningBelanja?->nama_rekening ?: ($item->asal_nama ?: '-') }}">
                                    {{ $item->astap?->rekeningBelanja?->kode_rekening ?: '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5">
                                    {{ $item->astap?->rekeningBelanja?->nama_rekening ?: ($item->asal_nama ?: '-') }}
                                </div>
                            </td>

                            {{-- 5. Nilai Semula --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono text-[11px] text-slate-300 font-semibold">
                                Rp {{ number_format($item->nilai_semula, 0, ',', '.') }}
                            </td>

                            {{-- 6. Koreksi Internal --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono">
                                <div class="text-xs sm:text-sm font-black {{ $tipe === 'tambah' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tipe === 'tambah' ? '+' : '-' }}Rp {{ number_format((float) $item->nilai_reklas, 0, ',', '.') }}
                                </div>
                                <div class="text-[9px] uppercase font-bold {{ $tipe === 'tambah' ? 'text-emerald-500/80' : 'text-rose-500/80' }}">
                                    {{ $tipe === 'tambah' ? 'Bertambah' : 'Berkurang' }}
                                </div>
                            </td>

                            {{-- 7. Nilai Pasca Koreksi --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono text-[11px] font-bold text-emerald-300">
                                Rp {{ number_format($item->nilai_setelah_koreksi, 0, ',', '.') }}
                            </td>

                            {{-- 8. Berita Acara Rekon Internal --}}
                            <td class="py-3.5 px-4 min-w-[190px]">
                                <div class="font-semibold text-slate-200 line-clamp-1 flex items-center gap-1.5" title="{{ $item->nomor_ba_reklas ?: '-' }}">
                                    <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>{{ $item->nomor_ba_reklas ?: '-' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5" title="{{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}">
                                    {{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}
                                </div>
                            </td>

                            {{-- 9. Dampak Kolom RMB --}}
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                @if ($tipe === 'tambah')
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm"
                                          title="Masuk ke Kolom 5 Kertas Kerja RMB BPKAD (Koreksi Rek Bertambah)">
                                        Kolom 5 (+)
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-sm"
                                          title="Masuk ke Kolom 15 Kertas Kerja RMB BPKAD (Koreksi Berkurang)">
                                        Kolom 15 (-)
                                    </span>
                                @endif
                            </td>

                            {{-- 10. Aksi (Sticky Freeze Column) --}}
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap sticky-aksi-koreksi">
                                <button type="button" @click="openDetailModal({{ $item->id }})"
                                        class="px-3 py-1.5 rounded-xl bg-indigo-900/40 hover:bg-indigo-800/60 active:bg-indigo-950 border border-indigo-700/50 text-indigo-200 hover:text-white font-bold text-[11px] transition-all flex items-center gap-1.5 mx-auto hover:-translate-y-0.5 active:scale-95 cursor-pointer shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
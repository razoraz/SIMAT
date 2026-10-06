{{-- TABEL 2: KHUSUS KOREKSI BIASA (INTERNAL RSUD DR. H. KOESNANDI) --}}
<div class="rounded-3xl bg-slate-900/90 border border-indigo-500/30 overflow-hidden shadow-2xl shadow-indigo-950/20 backdrop-blur-xl">
    <div class="p-5 border-b border-indigo-900/40 flex items-center justify-between flex-wrap gap-3 bg-gradient-to-r from-indigo-950/40 to-slate-950/60">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-indigo-900/50 border border-indigo-500/40 flex items-center justify-center text-sm text-indigo-300">
                🏢
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
        <div class="text-xs font-mono text-indigo-300">
            Total Transaksi: <strong class="text-white">{{ $biasaKoreksi->count() }}</strong>
        </div>
    </div>

    @if ($biasaKoreksi->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-indigo-950/40 border border-indigo-800/40 flex items-center justify-center mx-auto text-2xl text-indigo-400">
                🏢
            </div>
            <div class="text-white font-bold text-sm">Tidak Ditemukan Koreksi Nilai Biasa</div>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Belum ada transaksi koreksi biasa (rekonsiliasi internal) pada periode T.A. {{ $selectedYear }}.
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-indigo-900/40 bg-slate-950/80 text-[10px] uppercase font-bold text-indigo-300/80 tracking-wider">
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Tanggal &amp; TW</th>
                        <th class="py-3 px-4">Aset Tetap &amp; NIBAR</th>
                        <th class="py-3 px-4">Rekening Belanja / SP2D</th>
                        <th class="py-3 px-4 text-right">Nilai Semula</th>
                        <th class="py-3 px-4 text-right">Koreksi Internal</th>
                        <th class="py-3 px-4 text-right">Nilai Pasca Koreksi</th>
                        <th class="py-3 px-4">Berita Acara Rekon Internal</th>
                        <th class="py-3 px-4 text-center">Dampak Kolom RMB</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach ($biasaKoreksi as $idx => $item)
                        @php
                            $tipe = $item->tipe_koreksi;
                            $dampak = $item->dampak_rmb;
                        @endphp
                        <tr class="hover:bg-indigo-950/20 transition-colors group">
                            {{-- 1. No --}}
                            <td class="py-3 px-4 text-center font-mono text-slate-500 text-[11px]">
                                {{ $idx + 1 }}
                            </td>

                            {{-- 2. Tanggal & TW --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-semibold text-white">
                                    {{ $item->tanggal_reklas?->format('d/m/Y') ?: '-' }}
                                </div>
                                <div class="text-[10px] text-indigo-300 font-mono mt-0.5">
                                    TW {{ $item->triwulan }} · {{ $item->tahun }}
                                </div>
                            </td>

                            {{-- 3. Aset Tetap & NIBAR --}}
                            <td class="py-3 px-4 min-w-[200px]">
                                <div class="font-bold text-white group-hover:text-indigo-300 transition-colors line-clamp-1">
                                    {{ $item->astap?->nama_barang ?: ($item->asal_nama ?: 'Aset') }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 text-[10px] font-mono">
                                    <span class="text-amber-400 font-semibold">{{ $item->astap?->nibar ?: '-' }}</span>
                                    <span class="text-slate-500">·</span>
                                    <span class="px-1.5 py-0.2 rounded bg-indigo-950/60 text-indigo-300 border border-indigo-800/50">
                                        {{ $item->astap?->category ?: ($item->asal_kib ?: 'KIB') }}
                                    </span>
                                </div>
                            </td>

                            {{-- 4. Rekening Belanja / SP2D --}}
                            <td class="py-3 px-4 min-w-[180px]">
                                <div class="text-[11px] font-mono text-slate-300 line-clamp-1" title="{{ $item->astap?->rekeningBelanja?->nama_rekening ?: ($item->asal_nama ?: '-') }}">
                                    {{ $item->astap?->rekeningBelanja?->kode_rekening ?: '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5">
                                    {{ $item->astap?->rekeningBelanja?->nama_rekening ?: ($item->asal_nama ?: '-') }}
                                </div>
                            </td>

                            {{-- 5. Nilai Semula --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-[11px] text-slate-300">
                                Rp {{ number_format($item->nilai_semula, 2, ',', '.') }}
                            </td>

                            {{-- 6. Koreksi Internal --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono">
                                <div class="font-bold {{ $tipe === 'tambah' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tipe === 'tambah' ? '+' : '-' }} Rp {{ number_format((float) $item->nilai_reklas, 2, ',', '.') }}
                                </div>
                                <div class="text-[9px] uppercase font-bold {{ $tipe === 'tambah' ? 'text-emerald-500/80' : 'text-rose-500/80' }}">
                                    {{ $tipe === 'tambah' ? 'Bertambah' : 'Berkurang' }}
                                </div>
                            </td>

                            {{-- 7. Nilai Pasca Koreksi --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-[11px] font-bold text-white">
                                Rp {{ number_format($item->nilai_setelah_koreksi, 2, ',', '.') }}
                            </td>

                            {{-- 8. Berita Acara Rekon Internal --}}
                            <td class="py-3 px-4 min-w-[180px]">
                                <div class="font-semibold text-slate-200 line-clamp-1" title="{{ $item->nomor_ba_reklas ?: '-' }}">
                                    {{ $item->nomor_ba_reklas ?: '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5" title="{{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}">
                                    {{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}
                                </div>
                            </td>

                            {{-- 9. Dampak Kolom RMB --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if ($tipe === 'tambah')
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40"
                                          title="Masuk ke Kolom 5 Kertas Kerja RMB BPKAD (Koreksi Rek Bertambah)">
                                        Kolom 5 (+)
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40"
                                          title="Masuk ke Kolom 15 Kertas Kerja RMB BPKAD (Koreksi Berkurang)">
                                        Kolom 15 (-)
                                    </span>
                                @endif
                            </td>

                            {{-- 10. Aksi --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <button type="button" @click="openDetailModal({{ $item->id }})"
                                        class="px-3 py-1.5 rounded-xl bg-indigo-900/40 hover:bg-indigo-800/60 border border-indigo-700/50 text-indigo-200 hover:text-white font-bold text-[11px] transition-all flex items-center gap-1.5 mx-auto">
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

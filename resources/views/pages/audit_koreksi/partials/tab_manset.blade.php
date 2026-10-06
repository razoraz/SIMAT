{{-- TABEL 4: KHUSUS KOREKSI MANSET (MANAJEMEN ASET / E-MANSET BPKAD) --}}
<div class="rounded-3xl bg-slate-900/90 border border-emerald-500/30 overflow-hidden shadow-2xl shadow-emerald-950/20 backdrop-blur-xl">
    <div class="p-5 border-b border-emerald-900/40 flex items-center justify-between flex-wrap gap-3 bg-gradient-to-r from-emerald-950/40 to-slate-950/60">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-900/50 border border-emerald-500/40 flex items-center justify-center text-sm text-emerald-300">
                🔄
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
        <div class="text-xs font-mono text-emerald-300">
            Total Sinkronisasi: <strong class="text-white">{{ $mansetKoreksi->count() }}</strong>
        </div>
    </div>

    @if ($mansetKoreksi->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-emerald-950/40 border border-emerald-800/40 flex items-center justify-center mx-auto text-2xl text-emerald-400">
                🔄
            </div>
            <div class="text-white font-bold text-sm">Tidak Ditemukan Koreksi Nilai Manset</div>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Belum ada transaksi penyesuaian sinkronisasi E-Manset BPKAD pada periode T.A. {{ $selectedYear }}.
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-emerald-900/40 bg-slate-950/80 text-[10px] uppercase font-bold text-emerald-300/80 tracking-wider">
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Tanggal &amp; TW</th>
                        <th class="py-3 px-4">Nomor BA BPKAD / Manset</th>
                        <th class="py-3 px-4">Aset &amp; Kodefikasi 108</th>
                        <th class="py-3 px-4 text-right">Nilai SIMAT RSUD</th>
                        <th class="py-3 px-4 text-right">Koreksi Penyelarasan</th>
                        <th class="py-3 px-4 text-right">Nilai Akhir E-Manset</th>
                        <th class="py-3 px-4">Uraian Harmonisasi BPKAD</th>
                        <th class="py-3 px-4 text-center">Dampak Kolom RMB</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach ($mansetKoreksi as $idx => $item)
                        @php
                            $tipe = $item->tipe_koreksi;
                            $dampak = $item->dampak_rmb;
                        @endphp
                        <tr class="hover:bg-emerald-950/20 transition-colors group">
                            {{-- 1. No --}}
                            <td class="py-3 px-4 text-center font-mono text-slate-500 text-[11px]">
                                {{ $idx + 1 }}
                            </td>

                            {{-- 2. Tanggal & TW --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-semibold text-white">
                                    {{ $item->tanggal_reklas?->format('d/m/Y') ?: '-' }}
                                </div>
                                <div class="text-[10px] text-emerald-300 font-mono mt-0.5">
                                    TW {{ $item->triwulan }} · {{ $item->tahun }}
                                </div>
                            </td>

                            {{-- 3. Nomor BA BPKAD / Manset --}}
                            <td class="py-3 px-4 min-w-[190px]">
                                <div class="font-bold text-emerald-300 flex items-center gap-1.5 line-clamp-1" title="{{ $item->nomor_ba_reklas ?: '-' }}">
                                    <span>🏛️</span>
                                    <span>{{ $item->nomor_ba_reklas ?: 'BA BPKAD' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Bidang Pengelolaan BMD
                                </div>
                            </td>

                            {{-- 4. Aset & Kodefikasi 108 --}}
                            <td class="py-3 px-4 min-w-[200px]">
                                <div class="font-bold text-white group-hover:text-emerald-300 transition-colors line-clamp-1">
                                    {{ $item->astap?->nama_barang ?: ($item->asal_nama ?: 'Aset') }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 text-[10px] font-mono">
                                    <span class="text-amber-400 font-semibold">{{ $item->astap?->nibar ?: '-' }}</span>
                                    <span class="text-slate-500">·</span>
                                    <span class="px-1.5 py-0.2 rounded bg-emerald-950/60 text-emerald-300 border border-emerald-800/50">
                                        {{ $item->astap?->kode_108 ?: '-' }}
                                    </span>
                                </div>
                            </td>

                            {{-- 5. Nilai SIMAT RSUD --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-[11px] text-slate-300">
                                Rp {{ number_format($item->nilai_semula, 2, ',', '.') }}
                            </td>

                            {{-- 6. Koreksi Penyelarasan --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono">
                                <div class="font-bold {{ $tipe === 'tambah' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tipe === 'tambah' ? '+' : '-' }} Rp {{ number_format((float) $item->nilai_reklas, 2, ',', '.') }}
                                </div>
                                <div class="text-[9px] uppercase font-bold {{ $tipe === 'tambah' ? 'text-emerald-500/80' : 'text-rose-500/80' }}">
                                    {{ $tipe === 'tambah' ? 'Harmonisasi Tambah' : 'Harmonisasi Kurang' }}
                                </div>
                            </td>

                            {{-- 7. Nilai Akhir E-Manset --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-[11px] font-bold text-white">
                                Rp {{ number_format($item->nilai_setelah_koreksi, 2, ',', '.') }}
                            </td>

                            {{-- 8. Uraian Harmonisasi BPKAD --}}
                            <td class="py-3 px-4 min-w-[200px]">
                                <div class="text-slate-300 line-clamp-2 leading-snug" title="{{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}">
                                    {{ $item->alasan_reklas ?: ($item->keterangan ?: 'Harmonisasi register dengan SIMDA/E-Manset.') }}
                                </div>
                            </td>

                            {{-- 9. Dampak Kolom RMB --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if ($tipe === 'tambah')
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40"
                                          title="Masuk ke Kolom 7 Kertas Kerja RMB BPKAD (Koreksi Manset Bertambah)">
                                        Kolom 7 (+)
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40"
                                          title="Masuk ke Kolom 17 Kertas Kerja RMB BPKAD (Koreksi Manset Berkurang)">
                                        Kolom 17 (-)
                                    </span>
                                @endif
                            </td>

                            {{-- 10. Aksi --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <button type="button" @click="openDetailModal({{ $item->id }})"
                                        class="px-3 py-1.5 rounded-xl bg-emerald-900/40 hover:bg-emerald-800/60 border border-emerald-700/50 text-emerald-200 hover:text-white font-bold text-[11px] transition-all flex items-center gap-1.5 mx-auto">
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

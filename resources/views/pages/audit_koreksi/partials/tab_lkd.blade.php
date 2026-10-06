{{-- TABEL 3: KHUSUS KOREKSI LKD (AUDIT TEMUAN BPK RI / LKPD) --}}
<div class="rounded-3xl bg-slate-900/90 border border-cyan-500/30 overflow-hidden shadow-2xl shadow-cyan-950/20 backdrop-blur-xl">
    <div class="p-5 border-b border-cyan-900/40 flex items-center justify-between flex-wrap gap-3 bg-gradient-to-r from-cyan-950/40 to-slate-950/60">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-cyan-900/50 border border-cyan-500/40 flex items-center justify-center text-sm text-cyan-300">
                ⚖️
            </div>
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>Audit Koreksi Nilai LKD (Temuan Auditor BPK RI)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                        Kolom 6 &amp; 16 RMB
                    </span>
                </h3>
                <p class="text-[11px] text-cyan-200/70">
                    Penyesuaian nilai buku aset tetap berdasarkan Laporan Hasil Pemeriksaan (LHP) BPK RI atas Laporan Keuangan Daerah (LKPD).
                </p>
            </div>
        </div>
        <div class="text-xs font-mono text-cyan-300">
            Total Temuan: <strong class="text-white">{{ $lkdKoreksi->count() }}</strong>
        </div>
    </div>

    @if ($lkdKoreksi->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-cyan-950/40 border border-cyan-800/40 flex items-center justify-center mx-auto text-2xl text-cyan-400">
                ⚖️
            </div>
            <div class="text-white font-bold text-sm">Tidak Ditemukan Koreksi Nilai LKD</div>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Nihil temuan audit LHP BPK RI yang memerlukan penyesuaian nilai buku pada periode T.A. {{ $selectedYear }}.
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-cyan-900/40 bg-slate-950/80 text-[10px] uppercase font-bold text-cyan-300/80 tracking-wider">
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Tanggal &amp; TW</th>
                        <th class="py-3 px-4">Nomor LHP / Temuan BPK</th>
                        <th class="py-3 px-4">Aset Terperiksa &amp; NIBAR</th>
                        <th class="py-3 px-4 text-right">Nilai Audited Awal</th>
                        <th class="py-3 px-4 text-right">Koreksi Nilai BPK</th>
                        <th class="py-3 px-4 text-right">Nilai Audited Akhir</th>
                        <th class="py-3 px-4">Uraian Rekomendasi LHP BPK RI</th>
                        <th class="py-3 px-4 text-center">Dampak Kolom RMB</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach ($lkdKoreksi as $idx => $item)
                        @php
                            $tipe = $item->tipe_koreksi;
                            $dampak = $item->dampak_rmb;
                        @endphp
                        <tr class="hover:bg-cyan-950/20 transition-colors group">
                            {{-- 1. No --}}
                            <td class="py-3 px-4 text-center font-mono text-slate-500 text-[11px]">
                                {{ $idx + 1 }}
                            </td>

                            {{-- 2. Tanggal & TW --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-semibold text-white">
                                    {{ $item->tanggal_reklas?->format('d/m/Y') ?: '-' }}
                                </div>
                                <div class="text-[10px] text-cyan-300 font-mono mt-0.5">
                                    TW {{ $item->triwulan }} · {{ $item->tahun }}
                                </div>
                            </td>

                            {{-- 3. Nomor LHP / Temuan BPK --}}
                            <td class="py-3 px-4 min-w-[190px]">
                                <div class="font-bold text-cyan-300 flex items-center gap-1.5 line-clamp-1" title="{{ $item->nomor_ba_reklas ?: '-' }}">
                                    <span>📑</span>
                                    <span>{{ $item->nomor_ba_reklas ?: 'LHP BPK RI' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Audit Trail Kepatuhan BPK
                                </div>
                            </td>

                            {{-- 4. Aset Terperiksa & NIBAR --}}
                            <td class="py-3 px-4 min-w-[200px]">
                                <div class="font-bold text-white group-hover:text-cyan-300 transition-colors line-clamp-1">
                                    {{ $item->astap?->nama_barang ?: ($item->asal_nama ?: 'Aset Terperiksa') }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 text-[10px] font-mono">
                                    <span class="text-amber-400 font-semibold">{{ $item->astap?->nibar ?: '-' }}</span>
                                    <span class="text-slate-500">·</span>
                                    <span class="px-1.5 py-0.2 rounded bg-cyan-950/60 text-cyan-300 border border-cyan-800/50">
                                        {{ $item->astap?->category ?: ($item->asal_kib ?: 'KIB') }}
                                    </span>
                                </div>
                            </td>

                            {{-- 5. Nilai Audited Awal --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-[11px] text-slate-300">
                                Rp {{ number_format($item->nilai_semula, 2, ',', '.') }}
                            </td>

                            {{-- 6. Koreksi Nilai BPK --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono">
                                <div class="font-bold {{ $tipe === 'tambah' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tipe === 'tambah' ? '+' : '-' }} Rp {{ number_format((float) $item->nilai_reklas, 2, ',', '.') }}
                                </div>
                                <div class="text-[9px] uppercase font-bold {{ $tipe === 'tambah' ? 'text-emerald-500/80' : 'text-rose-500/80' }}">
                                    {{ $tipe === 'tambah' ? 'Koreksi Tambah BPK' : 'Koreksi Kurang BPK' }}
                                </div>
                            </td>

                            {{-- 7. Nilai Audited Akhir --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono text-[11px] font-bold text-white">
                                Rp {{ number_format($item->nilai_setelah_koreksi, 2, ',', '.') }}
                            </td>

                            {{-- 8. Uraian Rekomendasi LHP BPK RI --}}
                            <td class="py-3 px-4 min-w-[200px]">
                                <div class="text-slate-300 line-clamp-2 leading-snug" title="{{ $item->alasan_reklas ?: ($item->keterangan ?: '-') }}">
                                    {{ $item->alasan_reklas ?: ($item->keterangan ?: 'Rekomendasi audit LKPD BPK RI.') }}
                                </div>
                            </td>

                            {{-- 9. Dampak Kolom RMB --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if ($tipe === 'tambah')
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40"
                                          title="Masuk ke Kolom 6 Kertas Kerja RMB BPKAD (Koreksi LKD Bertambah)">
                                        Kolom 6 (+)
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40"
                                          title="Masuk ke Kolom 16 Kertas Kerja RMB BPKAD (Koreksi LKD Berkurang)">
                                        Kolom 16 (-)
                                    </span>
                                @endif
                            </td>

                            {{-- 10. Aksi --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <button type="button" @click="openDetailModal({{ $item->id }})"
                                        class="px-3 py-1.5 rounded-xl bg-cyan-900/40 hover:bg-cyan-800/60 border border-cyan-700/50 text-cyan-200 hover:text-white font-bold text-[11px] transition-all flex items-center gap-1.5 mx-auto">
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

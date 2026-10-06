<div class="space-y-6">
    {{-- Banner Penjelasan Uji Petik Neraca --}}
    <div class="p-4 rounded-2xl bg-cyan-950/20 border border-cyan-500/30 flex items-start space-x-3">
        <span class="text-cyan-400 text-lg shrink-0">💡</span>
        <div class="text-xs text-cyan-200/90 leading-relaxed">
            <strong>Mekanisme Rekonsiliasi Belanja Modal (Uji Petik Kas vs Fisik Aset):</strong><br>
            Tabel di bawah ini membuktikan apakah perolehan kas daerah di LRA BPKAD telah tercatat secara tertib dan seimbang (<em class="text-emerald-300 font-semibold">100% Balance</em>) dengan fisik barang di KIB. Saldo fisik Aset Tetap yang ada di KIB dinetralkan oleh 3 baris akun penyeimbang (Hibah, Ekstrakomptabel, dan Koreksi Lain-Lain).
        </div>
    </div>

    {{-- Kertas Kerja Tabel Format RSUD dr. H. Koesnandi --}}
    <div class="rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl overflow-hidden backdrop-blur-xl">
        <div class="p-4 border-b border-slate-800 bg-slate-950/60 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <span class="w-3 h-3 rounded-full bg-cyan-400"></span>
                <h3 class="text-xs sm:text-sm font-black text-white uppercase tracking-wider">
                    Lembar Rekonsiliasi Realisasi Belanja Modal Terhadap Mutasi Aset Tetap
                </h3>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 rounded-full text-[10px] font-mono font-black border"
                      :class="isBalance ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-xs' : 'bg-rose-500/20 text-rose-300 border-rose-500/40 shadow-xs'"
                      x-text="isBalance ? '✅ STATUS: BALANCE (0)' : '⚠️ STATUS: TERDAPAT SELISIH'"></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs font-mono">
                <thead class="bg-slate-950 text-slate-300 border-b border-slate-800 uppercase text-[10.5px] font-bold tracking-wider">
                    <tr>
                        <th class="p-3.5 border-r border-slate-800 w-12 text-center">No</th>
                        <th class="p-3.5 border-r border-slate-800">Uraian Rekonsiliasi Neraca</th>
                        <th class="p-3.5 border-r border-slate-800 text-right w-44">Saldo Awal Belanja</th>
                        <th class="p-3.5 border-r border-slate-800 text-right w-40 text-emerald-300">Mutasi Tambah (+)</th>
                        <th class="p-3.5 border-r border-slate-800 text-right w-40 text-rose-300">Mutasi Kurang (−)</th>
                        <th class="p-3.5 text-right w-44 text-cyan-300 font-black">Saldo Akhir</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-800/70 text-slate-200">
                    {{-- 1. JUMLAH ASET TETAP (A s/d F) --}}
                    <tr class="bg-slate-900/90 font-bold hover:bg-slate-800/40 transition-colors">
                        <td class="p-3 text-center border-r border-slate-800 text-slate-500">I</td>
                        <td class="p-3 border-r border-slate-800 text-white font-extrabold uppercase">
                            JUMLAH ASET TETAP (KIB A s/d F)
                        </td>
                        <td class="p-3 text-right border-r border-slate-800 font-bold text-cyan-300">
                            Rp {{ number_format($kertasKerja['saldo_awal_belanja'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-right border-r border-slate-800 text-emerald-300 font-bold">
                            +Rp {{ number_format($kertasKerja['mutasi_tambah_netto'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-right border-r border-slate-800 text-rose-300 font-bold">
                            -Rp {{ number_format($kertasKerja['mutasi_kurang'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-right font-black text-indigo-300 bg-slate-950/30">
                            Rp {{ number_format($kertasKerja['saldo_akhir_aset_tetap'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Header Bagian Koreksi --}}
                    <tr class="bg-slate-950/70 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <td class="p-2.5 text-center border-r border-slate-800">II</td>
                        <td colspan="5" class="p-2.5">
                            KOREKSI &amp; PENYEIMBANG ATAS ASET TETAP
                        </td>
                    </tr>

                    {{-- Baris 40: Hibah --}}
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="p-3 text-center border-r border-slate-800 text-slate-500">40</td>
                        <td class="p-3 border-r border-slate-800">
                            <div class="font-bold text-white">Koreksi Hibah / Bantuan Pemerintah</div>
                            <div class="text-[10px] text-slate-400">Penerimaan hibah masuk (−) / Penyerahan hibah keluar (+)</div>
                        </td>
                        <td class="p-3 text-right border-r border-slate-800 text-slate-500">Rp 0</td>
                        <td class="p-3 text-right border-r border-slate-800 text-emerald-300">
                            {{ $grandTotal['c10_dihibahkan'] > 0 ? '+Rp ' . number_format($grandTotal['c10_dihibahkan'], 0, ',', '.') : 'Rp 0' }}
                        </td>
                        <td class="p-3 text-right border-r border-slate-800 text-rose-300">
                            {{ $grandTotal['c2_hibah'] > 0 ? '-Rp ' . number_format($grandTotal['c2_hibah'], 0, ',', '.') : 'Rp 0' }}
                        </td>
                        <td class="p-3 text-right font-bold text-slate-300">
                            Rp {{ number_format($kertasKerja['koreksi_hibah'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Baris 41: Ekstrakomptabel --}}
                    <tr class="hover:bg-slate-800/30 transition-colors bg-amber-950/10">
                        <td class="p-3 text-center border-r border-slate-800 text-slate-500">41</td>
                        <td class="p-3 border-r border-slate-800">
                            <div class="font-bold text-amber-300">Dibawah Batas Kapitalisasi (Ekstrakomptabel)</div>
                            <div class="text-[10px] text-slate-400">Barang belanja modal dengan harga perolehan &le; Rp 300.000</div>
                        </td>
                        <td class="p-3 text-right border-r border-slate-800 text-slate-500">Rp 0</td>
                        <td class="p-3 text-right border-r border-slate-800 text-amber-300 font-extrabold">
                            +Rp {{ number_format($kertasKerja['koreksi_ekstrakom'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-right border-r border-slate-800 text-slate-500">Rp 0</td>
                        <td class="p-3 text-right font-black text-amber-300">
                            Rp {{ number_format($kertasKerja['koreksi_ekstrakom'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Baris 42: Koreksi Lain-Lain --}}
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="p-3 text-center border-r border-slate-800 text-slate-500">42</td>
                        <td class="p-3 border-r border-slate-800">
                            <div class="font-bold text-cyan-300">Koreksi Lain-Lain (Audit BPK &amp; Mutasi OPD)</div>
                            <div class="text-[10px] text-slate-400">Temuan audit LHP BPK, mutasi eksternal, rusak berat &amp; aset hilang</div>
                        </td>
                        <td class="p-3 text-right border-r border-slate-800 text-slate-500">Rp 0</td>
                        <td class="p-3 text-right border-r border-slate-800 text-emerald-300">
                            {{ $kertasKerja['koreksi_lain_lain'] > 0 ? '+Rp ' . number_format($kertasKerja['koreksi_lain_lain'], 0, ',', '.') : 'Rp 0' }}
                        </td>
                        <td class="p-3 text-right border-r border-slate-800 text-rose-300">
                            {{ $kertasKerja['koreksi_lain_lain'] < 0 ? '-Rp ' . number_format(abs($kertasKerja['koreksi_lain_lain']), 0, ',', '.') : 'Rp 0' }}
                        </td>
                        <td class="p-3 text-right font-bold text-cyan-300">
                            Rp {{ number_format($kertasKerja['koreksi_lain_lain'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>

                {{-- Footer Uji Petik Keseimbangan Belanja Modal --}}
                <tfoot class="border-t-2 border-slate-700 bg-slate-950 font-black">
                    {{-- Baris 1: Total Rekonsiliasi Belanja Modal --}}
                    <tr class="border-b border-slate-800 text-white bg-slate-950">
                        <td colspan="2" class="p-3.5 uppercase tracking-wider text-right text-emerald-400">
                            TOTAL REALISASI BELANJA MODAL (HASIL REKONSILIASI)
                        </td>
                        <td colspan="4" class="p-3.5 text-right font-mono text-sm text-emerald-300">
                            Rp {{ number_format($kertasKerja['total_rekon_belanja'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Baris 2: Realisasi Kasda di LRA --}}
                    <tr class="border-b border-slate-800 text-slate-300 bg-slate-950">
                        <td colspan="2" class="p-3.5 uppercase tracking-wider text-right text-cyan-400">
                            REALISASI BELANJA MODAL PADA LRA KAS DAERAH (SP2D)
                        </td>
                        <td colspan="4" class="p-3.5 text-right font-mono text-sm text-cyan-300">
                            Rp {{ number_format($kertasKerja['realisasi_kasda_lra'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Baris 3: Selisih Keseimbangan (Balance Check) --}}
                    <tr :class="isBalance ? 'bg-emerald-950/30 text-emerald-300' : 'bg-rose-950/30 text-rose-300'">
                        <td colspan="2" class="p-4 uppercase tracking-wider text-right flex items-center justify-end gap-2">
                            <span>⚖️</span>
                            <span>SELISIH KESEIMBANGAN (UJI PETIK RMB):</span>
                        </td>
                        <td colspan="4" class="p-4 text-right font-mono text-base font-black">
                            <span x-text="isBalance ? 'Rp 0 (100% BALANCE / SEIMBANG)' : 'TERDAPAT SELISIH Rp ' + Number(selisihNominal).toLocaleString('id-ID')"></span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

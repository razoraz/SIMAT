<!-- TABEL MATRIKS NERACA REKLASIFIKASI 5 KOLOM (PMDN 108) -->
<div class="bg-slate-900/90 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-xl">
    <div class="px-6 py-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-950/40">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>📊</span> Matriks Neraca Reklasifikasi Aset Tetap
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
                Formula: Saldo Akhir = Saldo Awal (Belanja Modal) + Mutasi Tambah - Mutasi Kurang
            </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <!-- Tombol Buka Kamus Panduan PMDN 108 -->
            <button type="button" @click="openPanduan('')"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 text-xs font-bold transition-all duration-200 cursor-pointer shadow-sm"
                title="Kamus & Panduan Pengelompokan Barang PMDN 108 Khas RSUD">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>📖 Kamus Panduan KIB</span>
            </button>
            <span class="text-xs font-mono font-semibold text-slate-400 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                Standar Sheet 3 RSDK
            </span>
        </div>
    </div>

    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-slate-700">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-slate-800/90 text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700 text-[11px]">
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4 min-w-[280px]">Uraian Kelompok & Sub-Rincian PMDN 108</th>
                    <th class="py-3.5 px-4 w-28 text-center font-mono">Kode Prefix</th>
                    <th class="py-3.5 px-4 min-w-[170px] text-right">Saldo Awal (Belanja Modal)</th>
                    <th class="py-3.5 px-4 min-w-[150px] text-right text-emerald-400">Mutasi Tambah (+)</th>
                    <th class="py-3.5 px-4 min-w-[150px] text-right text-rose-400">Mutasi Kurang (-)</th>
                    <th class="py-3.5 px-4 min-w-[170px] text-right text-indigo-300">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-medium">
                @php
                    $currentKib = null;
                @endphp

                @foreach ($matriks as $index => $row)
                    {{-- Header Grup KIB --}}
                    @if ($currentKib !== $row['kelompok_kib'])
                        @php
                            $currentKib = $row['kelompok_kib'];
                            $kibSubtotal = $subtotals[$currentKib] ?? ['awal' => 0, 'tambah' => 0, 'kurang' => 0, 'akhir' => 0];
                            $kibTitle = match ($currentKib) {
                                'KIB A' => 'KIB A · TANAH (1.3.1)',
                                'KIB B' => 'KIB B · PERALATAN DAN MESIN (1.3.2)',
                                'KIB C' => 'KIB C · GEDUNG DAN BANGUNAN (1.3.3)',
                                'KIB D' => 'KIB D · JALAN, IRIGASI DAN JARINGAN (1.3.4)',
                                'KIB E' => 'KIB E · ASET TETAP LAINNYA (1.3.5)',
                                'KIB F' => 'KIB F · KONSTRUKSI DALAM PENGERJAAN / KDP (1.3.6)',
                                'ASET LAINNYA' => 'ASET LAINNYA · KEMITRAAN / ATB / LAIN-LAIN (1.5)',
                                'KOREKSI' => 'KOREKSI & PENYEIMBANG ATAS ASET TETAP',
                                default => $currentKib,
                            };
                        @endphp
                        <tr class="bg-indigo-950/40 border-t-2 border-indigo-500/30">
                            <td colspan="3" class="py-3 px-4 font-black text-indigo-300 text-xs uppercase tracking-wider">
                                {{ $kibTitle }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-slate-300 font-mono">
                                Rp {{ number_format($kibSubtotal['awal'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-emerald-400 font-mono">
                                Rp {{ number_format($kibSubtotal['tambah'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-rose-400 font-mono">
                                Rp {{ number_format($kibSubtotal['kurang'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-indigo-300 font-mono">
                                Rp {{ number_format($kibSubtotal['akhir'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    {{-- Baris Detail Sub-Rincian --}}
                    <tr class="hover:bg-slate-800/40 transition-colors {{ $row['saldo_akhir'] > 0 || $row['saldo_awal'] > 0 ? 'bg-slate-900/30' : '' }}">
                        <td class="py-2.5 px-4 text-center text-slate-400 font-mono text-[11px]">
                            {{ $row['urutan'] }}
                        </td>
                        <td class="py-2.5 px-4 text-slate-200">
                            <div class="flex items-center justify-between gap-2">
                                <span class="{{ $row['saldo_akhir'] > 0 ? 'text-white font-bold' : 'text-slate-300' }}">
                                    {{ $row['nama_sub_rincian'] }}
                                </span>
                                <button type="button" @click="openPanduan('{{ $row['kode_prefix'] }}')"
                                    class="p-1 rounded-md text-slate-500 hover:text-indigo-300 hover:bg-indigo-500/20 transition-all cursor-pointer shrink-0"
                                    title="Lihat contoh & panduan barang {{ $row['nama_sub_rincian'] }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="py-2.5 px-4 text-center font-mono text-[11px] text-slate-400">
                            {{ $row['kode_prefix'] ?: '-' }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono {{ $row['saldo_awal'] > 0 ? 'text-white font-semibold' : 'text-slate-400' }}">
                            {{ number_format($row['saldo_awal'], 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono {{ $row['mutasi_tambah'] > 0 ? 'text-emerald-400 font-bold' : 'text-slate-400' }}">
                            {{ $row['mutasi_tambah'] > 0 ? '+' . number_format($row['mutasi_tambah'], 0, ',', '.') : '0' }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono {{ $row['mutasi_kurang'] > 0 ? 'text-rose-400 font-bold' : 'text-slate-400' }}">
                            {{ $row['mutasi_kurang'] > 0 ? '-' . number_format($row['mutasi_kurang'], 0, ',', '.') : '0' }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono font-bold {{ $row['saldo_akhir'] > 0 ? 'text-indigo-300' : 'text-slate-400' }}">
                            {{ number_format($row['saldo_akhir'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                {{-- GRAND TOTAL KESELURUHAN --}}
                <tr class="bg-slate-950 border-t-2 border-indigo-500 font-black text-xs text-white">
                    <td colspan="3" class="py-4 px-4 text-right uppercase tracking-wider text-indigo-400">
                        TOTAL KESELURUHAN ASET TETAP & KOREKSI
                    </td>
                    <td class="py-4 px-4 text-right font-mono text-white text-sm">
                        Rp {{ number_format($grandTotal['awal'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-4 text-right font-mono text-emerald-400 text-sm">
                        Rp {{ number_format($grandTotal['tambah'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-4 text-right font-mono text-rose-400 text-sm">
                        Rp {{ number_format($grandTotal['kurang'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-4 text-right font-mono text-indigo-300 text-sm">
                        Rp {{ number_format($grandTotal['akhir'], 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

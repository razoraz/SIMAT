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
            {{-- Tombol Kamus Panduan KIB --}}
            <button type="button"
                onclick="window.bukaKamusPanduanKib()"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-800 hover:bg-indigo-500/20 text-slate-300 hover:text-indigo-300 border border-slate-700 hover:border-indigo-500/50 transition-all cursor-pointer"
                title="Lihat kamus panduan pengelompokan KIB PMDN 108">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Kamus Panduan KIB</span>
            </button>

            <span class="text-xs font-mono font-semibold text-slate-400 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                Standar Sheet 3 RSDK
            </span>
        </div>
    </div>

    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-slate-700">
        <table class="w-full min-w-[1150px] text-left border-collapse text-xs">
            <thead>
                <tr class="bg-slate-800/90 text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700 text-[11px]">
                    <th class="py-3.5 px-4 w-12 text-center whitespace-nowrap">No</th>
                    <th class="py-3.5 px-4 min-w-[280px]">Uraian Kelompok &amp; Sub-Rincian PMDN 108</th>
                    <th class="py-3.5 px-4 w-28 text-center font-mono whitespace-nowrap">Kode Prefix</th>
                    <th class="py-3.5 px-4 min-w-[185px] text-right whitespace-nowrap">Saldo Awal (Belanja Modal)</th>
                    <th class="py-3.5 px-4 min-w-[185px] text-right text-emerald-400 whitespace-nowrap">Mutasi Tambah (+)</th>
                    <th class="py-3.5 px-4 min-w-[185px] text-right text-rose-400 whitespace-nowrap">Mutasi Kurang (-)</th>
                    <th class="py-3.5 pl-4 pr-6 min-w-[200px] text-right text-indigo-300 whitespace-nowrap">Saldo Akhir</th>
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
                            <td class="py-3 px-4 text-right font-black text-slate-300 font-mono whitespace-nowrap">
                                Rp&nbsp;{{ number_format($kibSubtotal['awal'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-emerald-400 font-mono whitespace-nowrap">
                                Rp&nbsp;{{ number_format($kibSubtotal['tambah'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-rose-400 font-mono whitespace-nowrap">
                                Rp&nbsp;{{ number_format($kibSubtotal['kurang'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 pl-4 pr-6 text-right font-black text-indigo-300 font-mono whitespace-nowrap">
                                Rp&nbsp;{{ number_format($kibSubtotal['akhir'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    {{-- Baris Detail Sub-Rincian --}}
                    @php
                        $isBaru = !empty($row['is_baru']);
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors {{ $isBaru ? 'bg-emerald-950/20 border-l-4 border-emerald-400' : ($row['saldo_akhir'] > 0 || $row['saldo_awal'] > 0 ? 'bg-slate-900/30' : '') }}">
                        <td class="py-2.5 px-4 text-center text-slate-400 font-mono text-[11px]">
                            {{ $row['urutan'] }}
                        </td>
                        <td class="py-2.5 px-4 text-slate-200">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="{{ $row['saldo_akhir'] > 0 ? 'text-white font-bold' : 'text-slate-300' }}">
                                        {{ $row['nama_sub_rincian'] }}
                                    </span>

                                    {{-- Penanda Khusus Transaksi Reklasifikasi yang Baru Saja diinput (< 24 Jam) --}}
                                    @if ($isBaru)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/35 cursor-help"
                                            title="{{ $row['reklas_tooltip_baru'] ?: 'Baru direklasifikasi dalam 24 jam terakhir' }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span>⚡ Baru Saja</span>
                                        </span>
                                    @endif
                                </div>

                                {{-- Tombol ⓘ: Lihat rincian barang yang direklasifikasi pada akun ini --}}
                                <button type="button"
                                    onclick="window.bukaRincianAkun({{ json_encode($row) }})"
                                    class="p-1 rounded-md text-slate-500 hover:text-indigo-300 hover:bg-indigo-500/20 transition-all cursor-pointer shrink-0"
                                    title="Lihat rincian barang pada {{ $row['nama_sub_rincian'] }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="py-2.5 px-4 text-center font-mono text-[11px] text-slate-400">
                            {{ $row['kode_prefix'] ?: '-' }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono whitespace-nowrap {{ $row['saldo_awal'] > 0 ? 'text-white font-semibold' : 'text-slate-400' }}">
                            {{ number_format($row['saldo_awal'], 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono whitespace-nowrap {{ $row['mutasi_tambah'] > 0 ? 'text-emerald-400 font-bold' : 'text-slate-400' }}">
                            {{ $row['mutasi_tambah'] > 0 ? '+' . number_format($row['mutasi_tambah'], 0, ',', '.') : '0' }}
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono whitespace-nowrap {{ $row['mutasi_kurang'] > 0 ? 'text-rose-400 font-bold' : 'text-slate-400' }}">
                            {{ $row['mutasi_kurang'] > 0 ? '-' . number_format($row['mutasi_kurang'], 0, ',', '.') : '0' }}
                        </td>
                        <td class="py-2.5 pl-4 pr-6 text-right font-mono font-bold whitespace-nowrap {{ $row['saldo_akhir'] > 0 ? 'text-indigo-300' : 'text-slate-400' }}">
                            {{ number_format($row['saldo_akhir'], 0, ',', '.') }}
                        </td>
                    </tr>

                    @php $nextRow = $matriks[$index + 1] ?? null; @endphp

                    {{-- SUBTOTAL: JUMLAH ASET TETAP (setelah baris terakhir KIB F) --}}
                    @if ($row['kelompok_kib'] === 'KIB F' && ($nextRow === null || in_array($nextRow['kelompok_kib'], ['ASET LAINNYA', 'KOREKSI'])))
                        <tr class="bg-blue-950/60 border-t-2 border-b-2 border-blue-500/40">
                            <td colspan="3" class="py-3 px-4 text-right font-black text-blue-200 text-xs uppercase tracking-wider">
                                JUMLAH ASET TETAP (KIB A s/d F)
                            </td>
                            <td class="py-3 px-4 text-right font-black font-mono text-blue-100 whitespace-nowrap">
                                Rp&nbsp;{{ number_format($jumlahAsetTetap['awal'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black font-mono text-emerald-300 whitespace-nowrap">
                                Rp&nbsp;{{ number_format($jumlahAsetTetap['tambah'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black font-mono text-rose-300 whitespace-nowrap">
                                Rp&nbsp;{{ number_format($jumlahAsetTetap['kurang'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 pl-4 pr-6 text-right font-black font-mono text-blue-200 whitespace-nowrap">
                                Rp&nbsp;{{ number_format($jumlahAsetTetap['akhir'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    {{-- SUBTOTAL: JUMLAH ASET LAINNYA (setelah baris terakhir ASET LAINNYA) --}}
                    @if ($row['kelompok_kib'] === 'ASET LAINNYA' && ($nextRow === null || $nextRow['kelompok_kib'] === 'KOREKSI'))
                        <tr class="bg-cyan-950/60 border-t-2 border-b-2 border-cyan-500/40">
                            <td colspan="3" class="py-3 px-4 text-right font-black text-cyan-200 text-xs uppercase tracking-wider">
                                JUMLAH ASET LAINNYA (1.5.2, 1.5.3, 1.5.4)
                            </td>
                            <td class="py-3 px-4 text-right font-black font-mono text-cyan-100 whitespace-nowrap">
                                Rp&nbsp;{{ number_format($jumlahAsetLainnya['awal'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black font-mono text-emerald-300 whitespace-nowrap">
                                Rp&nbsp;{{ number_format($jumlahAsetLainnya['tambah'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black font-mono text-rose-300 whitespace-nowrap">
                                Rp&nbsp;{{ number_format($jumlahAsetLainnya['kurang'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 pl-4 pr-6 text-right font-black font-mono text-cyan-200 whitespace-nowrap">
                                Rp&nbsp;{{ number_format($jumlahAsetLainnya['akhir'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                {{-- GRAND TOTAL = JUMLAH ASET TETAP + JUMLAH ASET LAINNYA (tidak include KOREKSI) --}}
                <tr class="bg-slate-950 border-t-2 border-indigo-500 font-black text-xs text-white">
                    <td colspan="3" class="py-4 px-4 text-right uppercase tracking-wider text-indigo-400">
                        JUMLAH ASET (ASET TETAP + ASET LAINNYA)
                    </td>
                    <td class="py-4 px-4 text-right font-mono text-white text-xs sm:text-sm whitespace-nowrap">
                        Rp&nbsp;{{ number_format($grandTotal['awal'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-4 text-right font-mono text-emerald-400 text-xs sm:text-sm whitespace-nowrap">
                        Rp&nbsp;{{ number_format($grandTotal['tambah'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-4 text-right font-mono text-rose-400 text-xs sm:text-sm whitespace-nowrap">
                        Rp&nbsp;{{ number_format($grandTotal['kurang'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 pl-4 pr-6 text-right font-mono text-indigo-300 text-xs sm:text-sm whitespace-nowrap">
                        Rp&nbsp;{{ number_format($grandTotal['akhir'], 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

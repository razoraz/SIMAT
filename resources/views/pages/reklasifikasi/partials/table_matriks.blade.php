<!-- TABEL MATRIKS NERACA REKLASIFIKASI 5 KOLOM (PMDN 108 & STANDAR SHEET 3 RSDK) -->
<div class="space-y-4">
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
                <span class="text-xs font-mono font-semibold text-cyan-300 bg-cyan-950/60 px-3 py-1.5 rounded-lg border border-cyan-800/60 shadow-inner">
                    Standar Sheet 3 RSDK · PMDN 108
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
                                $headerBg = match ($currentKib) {
                                    'KOREKSI' => 'bg-purple-950/40 border-t-2 border-purple-500/40 text-purple-300',
                                    'ASET LAINNYA' => 'bg-cyan-950/40 border-t-2 border-cyan-500/40 text-cyan-300',
                                    default => 'bg-indigo-950/40 border-t-2 border-indigo-500/30 text-indigo-300',
                                };
                            @endphp
                            <tr class="{{ $headerBg }}">
                                <td colspan="3" class="py-3 px-4 font-black text-xs uppercase tracking-wider">
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
                            <td class="py-2.5 px-4 text-right font-mono whitespace-nowrap {{ $row['saldo_awal'] > 0 ? 'text-white font-semibold' : 'text-slate-500' }}">
                                {{ number_format($row['saldo_awal'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono whitespace-nowrap {{ $row['mutasi_tambah'] > 0 ? 'text-emerald-400 font-bold' : 'text-slate-500' }}">
                                {{ $row['mutasi_tambah'] > 0 ? '+' . number_format($row['mutasi_tambah'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono whitespace-nowrap {{ $row['mutasi_kurang'] > 0 ? 'text-rose-400 font-bold' : 'text-slate-500' }}">
                                {{ $row['mutasi_kurang'] > 0 ? '-' . number_format($row['mutasi_kurang'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="py-2.5 pl-4 pr-6 text-right font-mono font-bold whitespace-nowrap {{ $row['saldo_akhir'] > 0 ? 'text-indigo-300' : 'text-slate-500' }}">
                                {{ number_format($row['saldo_akhir'], 0, ',', '.') }}
                            </td>
                        </tr>

                        @php $nextRow = $matriks[$index + 1] ?? null; @endphp

                        {{-- SUBTOTAL 1: JUMLAH ASET TETAP (setelah baris terakhir KIB F) --}}
                        @if ($row['kelompok_kib'] === 'KIB F' && ($nextRow === null || in_array($nextRow['kelompok_kib'], ['ASET LAINNYA', 'KOREKSI'])))
                            <tr class="bg-blue-950/70 border-t-2 border-b-2 border-blue-500/50">
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

                        {{-- SUBTOTAL 2: JUMLAH ASET LAINNYA (setelah baris terakhir ASET LAINNYA) --}}
                        @if ($row['kelompok_kib'] === 'ASET LAINNYA' && ($nextRow === null || $nextRow['kelompok_kib'] === 'KOREKSI'))
                            <tr class="bg-cyan-950/70 border-t-2 border-b-2 border-cyan-500/50">
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

                            {{-- GRAND TOTAL FISIK: JUMLAH ASET (ASET TETAP + ASET LAINNYA) --}}
                            {{-- Sesuai format baku Sheet 3 RSUD Koesnandi / BPKAD: Ditempatkan sebelum bagian Akun Penyeimbang --}}
                            <tr class="bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-950 border-t-2 border-b-2 border-indigo-400 font-black text-xs text-white shadow-xl">
                                <td colspan="3" class="py-3.5 px-4 text-right uppercase tracking-wider text-indigo-300">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-sm">🏛️</span>
                                        <span class="font-black text-indigo-200">JUMLAH ASET (ASET TETAP + ASET LAINNYA)</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-white text-xs sm:text-sm whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($grandTotal['awal'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-emerald-400 text-xs sm:text-sm whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($grandTotal['tambah'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-rose-400 text-xs sm:text-sm whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($grandTotal['kurang'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 pl-4 pr-6 text-right font-mono text-indigo-200 text-xs sm:text-sm whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($grandTotal['akhir'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif

                        {{-- SUBTOTAL 3: JUMLAH KOREKSI & PENYEIMBANG ATAS ASET TETAP (setelah baris 42) --}}
                        @if ($row['kelompok_kib'] === 'KOREKSI' && $nextRow === null)
                            <tr class="bg-purple-950/70 border-t-2 border-b-2 border-purple-500/50">
                                <td colspan="3" class="py-3 px-4 text-right font-black text-purple-200 text-xs uppercase tracking-wider">
                                    JUMLAH KOREKSI &amp; PENYEIMBANG ATAS ASET TETAP
                                </td>
                                <td class="py-3 px-4 text-right font-black font-mono text-purple-100 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($jumlahPenyeimbang['awal'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-black font-mono text-emerald-300 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($jumlahPenyeimbang['tambah'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-black font-mono text-rose-300 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($jumlahPenyeimbang['kurang'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 pl-4 pr-6 text-right font-black font-mono text-purple-200 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($jumlahPenyeimbang['akhir'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-slate-700 bg-slate-950 font-black">
                    {{-- 1. REALISASI BELANJA MODAL (HASIL REKONSILIASI FISIK KIB & PENYEIMBANG) --}}
                    <tr class="border-b border-slate-800 text-white bg-slate-950/90">
                        <td colspan="3" class="py-3.5 px-4 uppercase tracking-wider text-right text-emerald-300">
                            <div class="flex items-center justify-end gap-2">
                                <span>📌</span>
                                <span>REALISASI BELANJA MODAL (SETELAH REKLASIFIKASI)</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono text-white text-xs sm:text-sm whitespace-nowrap">
                            Rp&nbsp;{{ number_format($grandTotal['awal'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono text-emerald-400 text-xs sm:text-sm whitespace-nowrap">
                            Rp&nbsp;0
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono text-rose-400 text-xs sm:text-sm whitespace-nowrap">
                            Rp&nbsp;{{ number_format($grandTotal['kurang'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 pl-4 pr-6 text-right font-mono text-emerald-300 text-xs sm:text-sm whitespace-nowrap">
                            Rp&nbsp;{{ number_format($grandTotal['akhir'], 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 2. KONTROL KESEIMBANGAN (BALANCE CHECK SHEET 3 BPKAD) --}}
                    <tr class="{{ $isBalance ? 'bg-emerald-950/40 text-emerald-300' : 'bg-rose-950/40 text-rose-300' }} border-t border-slate-800">
                        <td colspan="3" class="py-4 px-4 uppercase tracking-wider text-right">
                            <div class="flex flex-col items-end">
                                <div class="flex items-center gap-2 font-black text-xs sm:text-sm">
                                    <span>⚖️</span>
                                    <span>KONTROL KESEIMBANGAN (BALANCE CHECK SHEET 3 BPKAD)</span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-normal lowercase tracking-normal">
                                    uji petik kasda (sp2d) vs fisik kib &amp; akun penyeimbang neraca
                                </span>
                            </div>
                        </td>
                        <td colspan="4" class="py-4 pl-4 pr-6 text-right font-mono">
                            @if ($isBalance)
                                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 shadow-sm shadow-emerald-500/10">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="font-black text-xs sm:text-sm tracking-wide">
                                        Rp&nbsp;0 · 100% BALANCE (SEIMBANG)
                                    </span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 shadow-sm shadow-rose-500/10">
                                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-ping"></span>
                                    <span class="font-black text-xs sm:text-sm tracking-wide">
                                        TERDAPAT SELISIH Rp&nbsp;{{ number_format($selisihKeseimbangan, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- CARD CATATAN AUDIT / KETERANGAN REKLASIFIKASI KHAS SHEET 3 RSUD -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl backdrop-blur-xl space-y-3">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-xs">
                    📝
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-white tracking-wide">
                        Keterangan &amp; Catatan Rekonsiliasi Neraca (Sheet 3 Notes)
                    </h4>
                    <p class="text-[10.5px] text-slate-400">
                        Dasar penyesuaian belanja modal dan pemindahan bukuan antar kelompok KIB periode Tahun {{ $selectedTahun }}
                    </p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-semibold px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 border border-slate-700">
                Audited SIMAT-RK
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
            <!-- 1. Koreksi Hibah -->
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-indigo-500/30 transition-colors">
                <div class="flex items-center justify-between text-indigo-300 font-bold mb-1">
                    <span>1. Koreksi Hibah</span>
                    <span class="font-mono text-emerald-400">+Rp&nbsp;{{ number_format($matriks[39]['mutasi_tambah'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Penerimaan hibah barang modal dari Kementerian / Pemprov masuk ke KIB tanpa melalui SP2D Kas Daerah RSUD, sehingga dinetralkan oleh akun penyeimbang kredit.
                </p>
            </div>

            <!-- 2. Ekstrakomptabel -->
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-amber-500/30 transition-colors">
                <div class="flex items-center justify-between text-amber-300 font-bold mb-1">
                    <span>2. Ekstrakomptabel (&le; Rp 300rb)</span>
                    <span class="font-mono text-amber-400">+Rp&nbsp;{{ number_format($matriks[40]['mutasi_tambah'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Barang dengan harga satuan di bawah batas kapitalisasi dikeluarkan dari KIB A–F dan dibukukan ke Daftar Ekstrakomptabel terpisah sesuai PMDN 108.
                </p>
            </div>

            <!-- 3. Koreksi Lain-Lain -->
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-cyan-500/30 transition-colors">
                <div class="flex items-center justify-between text-cyan-300 font-bold mb-1">
                    <span>3. Koreksi Lain-Lain (BPK &amp; Manset)</span>
                    <span class="font-mono text-cyan-400">{{ ($matriks[41]['mutasi_tambah'] ?? 0) > 0 ? '+Rp ' . number_format($matriks[41]['mutasi_tambah'], 0, ',', '.') : 'Rp 0' }}</span>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Harmonisasi saldo dengan audit LHP BPK RI, mutasi eksternal antar SKPD, dan sinkronisasi saldo awal master aset E-Manset BPKAD Kab. Bondowoso.
                </p>
            </div>
        </div>
    </div>
</div>

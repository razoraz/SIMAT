<div class="rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl overflow-hidden backdrop-blur-xl">
    <div class="p-4 border-b border-slate-800 bg-slate-950/60 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center space-x-2.5">
            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
            <h2 class="text-xs sm:text-sm font-black text-white uppercase tracking-wider">
                Matriks Mutasi 21 Kolom Rekapitulasi Mutasi Barang (RMB)
            </h2>
            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700">
                Format Lembar Kerja BPKAD
            </span>
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
            Tahun Anggaran: <strong class="text-white font-mono">{{ $selectedTahun }}</strong>
            @if ($selectedTw !== 'all')
                | Triwulan: <strong class="text-emerald-300 font-mono">{{ $selectedTw }}</strong>
            @else
                | <strong class="text-cyan-300">Seluruh Triwulan</strong>
            @endif
        </div>
    </div>

    {{-- Container Tabel Scroll Horizontal --}}
    <div class="overflow-x-auto max-h-[700px] overflow-y-auto custom-scrollbar">
        <table class="w-full text-left border-collapse text-[11px]">
            <thead class="sticky top-0 z-20 bg-slate-950 text-slate-300 font-mono select-none shadow-md">
                {{-- Baris Header 1: Grup Penambahan & Pengurangan --}}
                <tr class="border-b border-slate-800 text-center font-bold tracking-wider uppercase text-[10px]">
                    <th rowspan="2" class="p-3 border-r border-slate-800 min-w-[240px] text-left bg-slate-950 sticky left-0 z-30 shadow-r">
                        Uraian Akun PMDN 108
                    </th>
                    <th rowspan="2" class="p-3 border-r border-slate-800 min-w-[130px] bg-slate-950 text-cyan-300">
                        Saldo Awal<br><span class="text-[9px] text-slate-400 font-normal">(Belanja Kas)</span>
                    </th>
                    <th colspan="9" class="p-2 border-r border-slate-800 bg-emerald-950/50 text-emerald-300 border-b border-emerald-500/30">
                        PENAMBAHAN (+)
                    </th>
                    <th colspan="11" class="p-2 border-r border-slate-800 bg-rose-950/50 text-rose-300 border-b border-rose-500/30">
                        PENGURANGAN (−)
                    </th>
                    <th rowspan="2" class="p-3 min-w-[140px] bg-slate-950 text-emerald-300 font-black">
                        Saldo Akhir<br><span class="text-[9px] text-slate-400 font-normal">(Neraca BMD)</span>
                    </th>
                </tr>

                {{-- Baris Header 2: Rincian 21 Kolom Baku BPKAD --}}
                <tr class="border-b border-slate-800 text-center text-[9.5px] font-extrabold uppercase tracking-tight">
                    {{-- 8 Kolom Penambahan --}}
                    <th class="p-2 border-r border-slate-800/80 bg-emerald-950/30 min-w-[110px] text-emerald-200">Belanja Modal</th>
                    <th class="p-2 border-r border-slate-800/80 bg-emerald-950/30 min-w-[100px] text-emerald-200">Hibah</th>
                    <th class="p-2 border-r border-slate-800/80 bg-emerald-950/30 min-w-[100px] text-emerald-200">Belanja Barang</th>
                    <th class="p-2 border-r border-slate-800/80 bg-emerald-950/30 min-w-[100px] text-emerald-200">Mutasi (+)</th>
                    <th class="p-2 border-r border-slate-800/80 bg-emerald-950/30 min-w-[110px] text-emerald-200">Koreksi Rekening</th>
                    <th class="p-2 border-r border-slate-800/80 bg-emerald-950/30 min-w-[100px] text-emerald-200">Koreksi LKD</th>
                    <th class="p-2 border-r border-slate-800/80 bg-emerald-950/30 min-w-[100px] text-emerald-200">Koreksi Manset</th>
                    <th class="p-2 border-r border-slate-800/80 bg-emerald-950/30 min-w-[100px] text-emerald-200">KDP</th>
                    <th class="p-2 border-r border-slate-800 bg-emerald-900/40 min-w-[120px] text-emerald-300 font-black">Total Tambah</th>

                    {{-- 10 Kolom Pengurangan --}}
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[110px] text-rose-200">SK Penghapusan</th>
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[100px] text-rose-200">Dihibahkan</th>
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[100px] text-rose-200">Mutasi (−)</th>
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[110px] text-amber-300 font-black" title="Koreksi Batas Nilai Kapitalisasi (Ekstrakomptabel)">Kapitalisasi (−)</th>
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[110px] text-rose-200">Direklas Aset Lain</th>
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[90px] text-rose-200">Hilang</th>
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[100px] text-rose-200">Koreksi (−)</th>
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[100px] text-rose-200">Koreksi LKD</th>
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[100px] text-rose-200">Koreksi Manset</th>
                    <th class="p-2 border-r border-slate-800/80 bg-rose-950/30 min-w-[100px] text-rose-200">KDP</th>
                    <th class="p-2 border-r border-slate-800 bg-rose-900/40 min-w-[120px] text-rose-300 font-black">Total Kurang</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800/60 font-mono text-slate-300">
                @php
                    $groups = [
                        'KIB A' => ['name' => 'KIB A - TANAH', 'color' => 'amber'],
                        'KIB B' => ['name' => 'KIB B - PERALATAN DAN MESIN', 'color' => 'blue'],
                        'KIB C' => ['name' => 'KIB C - GEDUNG DAN BANGUNAN', 'color' => 'emerald'],
                        'KIB D' => ['name' => 'KIB D - JALAN, JARINGAN DAN IRIGASI', 'color' => 'purple'],
                        'KIB E' => ['name' => 'KIB E - ASET TETAP LAINNYA', 'color' => 'teal'],
                        'KIB F' => ['name' => 'KIB F - KONSTRUKSI DALAM PENGERJAAN', 'color' => 'rose'],
                    ];
                @endphp

                @foreach ($groups as $gk => $gMeta)
                    @php
                        $sub = $subtotals[$gk] ?? [];
                        $groupRows = array_filter($rmbRows, fn($r) => $r['group_key'] === $gk);
                    @endphp

                    {{-- Baris Accordion Header Kelompok KIB --}}
                    <tr class="bg-slate-950/90 hover:bg-slate-900/90 transition-all font-bold cursor-pointer select-none border-t border-b border-slate-700/80"
                        @click="toggleGroup('{{ $gk }}')">
                        <td class="p-2.5 sticky left-0 z-10 bg-slate-950 shadow-r flex items-center space-x-2">
                            <span class="text-xs transition-transform duration-200"
                                  :class="isGroupOpen('{{ $gk }}') ? 'rotate-90 text-emerald-400' : 'text-slate-500'">▶</span>
                            <span class="text-xs text-white uppercase tracking-wider">{{ $gMeta['name'] }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[9.5px] font-mono bg-slate-800 text-slate-400 border border-slate-700">
                                {{ count($groupRows) }} Akun
                            </span>
                        </td>
                        {{-- Saldo Awal Subtotal --}}
                        <td class="p-2.5 text-right font-black text-cyan-300">
                            {{ number_format($sub['saldo_awal'] ?? 0, 0, ',', '.') }}
                        </td>
                        {{-- Penambahan Subtotal --}}
                        <td class="p-2.5 text-right text-emerald-300/80">{{ number_format($sub['c1_belanja_modal'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-emerald-300/80">{{ number_format($sub['c2_hibah'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-emerald-300/80">{{ number_format($sub['c3_belanja_barang'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-emerald-300/80">{{ number_format($sub['c4_mutasi_tambah'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-emerald-300/80">{{ number_format($sub['c5_koreksi_rek_tambah'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-emerald-300/80">{{ number_format($sub['c6_koreksi_lkd_tambah'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-emerald-300/80">{{ number_format($sub['c7_koreksi_manset_tambah'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-emerald-300/80">{{ number_format($sub['c8_kdp_tambah'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right font-black text-emerald-300 bg-emerald-950/30">
                            {{ number_format($sub['total_penambahan'] ?? 0, 0, ',', '.') }}
                        </td>
                        {{-- Pengurangan Subtotal --}}
                        <td class="p-2.5 text-right text-rose-300/80">{{ number_format($sub['c9_sk_penghapusan'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-rose-300/80">{{ number_format($sub['c10_dihibahkan'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-rose-300/80">{{ number_format($sub['c11_mutasi_kurang'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-amber-300 font-extrabold">{{ number_format($sub['c12_kapitalisasi_kurang'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-rose-300/80">{{ number_format($sub['c13_direklas_aset_lain'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-rose-300/80">{{ number_format($sub['c14_hilang'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-rose-300/80">{{ number_format($sub['c15_koreksi_kurang'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-rose-300/80">{{ number_format($sub['c16_koreksi_lkd_kurang'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-rose-300/80">{{ number_format($sub['c17_koreksi_manset_kurang'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right text-rose-300/80">{{ number_format($sub['c18_kdp_kurang'] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right font-black text-rose-300 bg-rose-950/30">
                            {{ number_format($sub['total_pengurangan'] ?? 0, 0, ',', '.') }}
                        </td>
                        {{-- Saldo Akhir Subtotal --}}
                        <td class="p-2.5 text-right font-black text-indigo-300 bg-slate-950">
                            {{ number_format($sub['saldo_akhir'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Baris Detail Sub Rincian Akun PMDN 108 --}}
                    @foreach ($groupRows as $row)
                        <tr x-show="isGroupOpen('{{ $gk }}')" x-transition
                            class="hover:bg-slate-800/40 transition-colors {{ ($row['saldo_awal'] > 0 || $row['total_penambahan'] > 0 || $row['total_pengurangan'] > 0) ? 'bg-slate-900/50' : 'opacity-65' }}">
                            {{-- Uraian & Kode --}}
                            <td class="p-2 pl-7 sticky left-0 z-10 bg-slate-900 shadow-r border-r border-slate-800">
                                <div class="font-mono text-[10px] text-slate-400 font-bold leading-none mb-0.5">
                                    {{ $row['code'] }}
                                </div>
                                <div class="text-[11px] font-semibold text-slate-200 truncate max-w-[230px]" title="{{ $row['label'] }}">
                                    {{ $row['label'] }}
                                </div>
                            </td>

                            {{-- Saldo Awal --}}
                            <td class="p-2 text-right border-r border-slate-800 {{ $row['saldo_awal'] > 0 ? 'text-white font-bold' : 'text-slate-600' }}">
                                {{ $row['saldo_awal'] > 0 ? number_format($row['saldo_awal'], 0, ',', '.') : '0' }}
                            </td>

                            {{-- 8 Kolom Penambahan --}}
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c1_belanja_modal'] > 0 ? 'text-emerald-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c1_belanja_modal'] > 0 ? number_format($row['c1_belanja_modal'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c2_hibah'] > 0 ? 'text-emerald-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c2_hibah'] > 0 ? number_format($row['c2_hibah'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c3_belanja_barang'] > 0 ? 'text-emerald-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c3_belanja_barang'] > 0 ? number_format($row['c3_belanja_barang'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c4_mutasi_tambah'] > 0 ? 'text-emerald-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c4_mutasi_tambah'] > 0 ? number_format($row['c4_mutasi_tambah'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c5_koreksi_rek_tambah'] > 0 ? 'text-emerald-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c5_koreksi_rek_tambah'] > 0 ? number_format($row['c5_koreksi_rek_tambah'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c6_koreksi_lkd_tambah'] > 0 ? 'text-emerald-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c6_koreksi_lkd_tambah'] > 0 ? number_format($row['c6_koreksi_lkd_tambah'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c7_koreksi_manset_tambah'] > 0 ? 'text-emerald-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c7_koreksi_manset_tambah'] > 0 ? number_format($row['c7_koreksi_manset_tambah'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c8_kdp_tambah'] > 0 ? 'text-emerald-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c8_kdp_tambah'] > 0 ? number_format($row['c8_kdp_tambah'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800 bg-emerald-950/20 font-bold {{ $row['total_penambahan'] > 0 ? 'text-emerald-300' : 'text-slate-600' }}">
                                {{ $row['total_penambahan'] > 0 ? number_format($row['total_penambahan'], 0, ',', '.') : '0' }}
                            </td>

                            {{-- 10 Kolom Pengurangan --}}
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c9_sk_penghapusan'] > 0 ? 'text-rose-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c9_sk_penghapusan'] > 0 ? number_format($row['c9_sk_penghapusan'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c10_dihibahkan'] > 0 ? 'text-rose-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c10_dihibahkan'] > 0 ? number_format($row['c10_dihibahkan'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c11_mutasi_kurang'] > 0 ? 'text-rose-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c11_mutasi_kurang'] > 0 ? number_format($row['c11_mutasi_kurang'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c12_kapitalisasi_kurang'] > 0 ? 'text-amber-300 font-black' : 'text-slate-600' }}">
                                {{ $row['c12_kapitalisasi_kurang'] > 0 ? number_format($row['c12_kapitalisasi_kurang'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c13_direklas_aset_lain'] > 0 ? 'text-rose-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c13_direklas_aset_lain'] > 0 ? number_format($row['c13_direklas_aset_lain'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c14_hilang'] > 0 ? 'text-rose-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c14_hilang'] > 0 ? number_format($row['c14_hilang'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c15_koreksi_kurang'] > 0 ? 'text-rose-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c15_koreksi_kurang'] > 0 ? number_format($row['c15_koreksi_kurang'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c16_koreksi_lkd_kurang'] > 0 ? 'text-rose-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c16_koreksi_lkd_kurang'] > 0 ? number_format($row['c16_koreksi_lkd_kurang'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c17_koreksi_manset_kurang'] > 0 ? 'text-rose-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c17_koreksi_manset_kurang'] > 0 ? number_format($row['c17_koreksi_manset_kurang'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800/80 {{ $row['c18_kdp_kurang'] > 0 ? 'text-rose-300 font-bold' : 'text-slate-600' }}">
                                {{ $row['c18_kdp_kurang'] > 0 ? number_format($row['c18_kdp_kurang'], 0, ',', '.') : '0' }}
                            </td>
                            <td class="p-2 text-right border-r border-slate-800 bg-rose-950/20 font-bold {{ $row['total_pengurangan'] > 0 ? 'text-rose-300' : 'text-slate-600' }}">
                                {{ $row['total_pengurangan'] > 0 ? number_format($row['total_pengurangan'], 0, ',', '.') : '0' }}
                            </td>

                            {{-- Saldo Akhir --}}
                            <td class="p-2 text-right font-black {{ $row['saldo_akhir'] > 0 ? 'text-white' : 'text-slate-600' }}">
                                {{ $row['saldo_akhir'] > 0 ? number_format($row['saldo_akhir'], 0, ',', '.') : '0' }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>

            {{-- Footer Grand Total --}}
            <tfoot class="sticky bottom-0 z-20 bg-slate-950 text-white font-mono font-black text-xs border-t-2 border-emerald-500/50 shadow-2xl">
                <tr class="bg-slate-950">
                    <td class="p-3 sticky left-0 z-30 bg-slate-950 shadow-r uppercase tracking-wider text-emerald-400">
                        TOTAL ASET TETAP (KIB A s/d F)
                    </td>
                    <td class="p-3 text-right text-cyan-300">
                        {{ number_format($grandTotal['saldo_awal'] ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="p-3 text-right text-emerald-300">{{ number_format($grandTotal['c1_belanja_modal'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-emerald-300">{{ number_format($grandTotal['c2_hibah'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-emerald-300">{{ number_format($grandTotal['c3_belanja_barang'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-emerald-300">{{ number_format($grandTotal['c4_mutasi_tambah'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-emerald-300">{{ number_format($grandTotal['c5_koreksi_rek_tambah'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-emerald-300">{{ number_format($grandTotal['c6_koreksi_lkd_tambah'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-emerald-300">{{ number_format($grandTotal['c7_koreksi_manset_tambah'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-emerald-300">{{ number_format($grandTotal['c8_kdp_tambah'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-emerald-300 bg-emerald-950/40">
                        {{ number_format($grandTotal['total_penambahan'] ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="p-3 text-right text-rose-300">{{ number_format($grandTotal['c9_sk_penghapusan'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-rose-300">{{ number_format($grandTotal['c10_dihibahkan'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-rose-300">{{ number_format($grandTotal['c11_mutasi_kurang'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-amber-300 font-extrabold">{{ number_format($grandTotal['c12_kapitalisasi_kurang'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-rose-300">{{ number_format($grandTotal['c13_direklas_aset_lain'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-rose-300">{{ number_format($grandTotal['c14_hilang'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-rose-300">{{ number_format($grandTotal['c15_koreksi_kurang'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-rose-300">{{ number_format($grandTotal['c16_koreksi_lkd_kurang'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-rose-300">{{ number_format($grandTotal['c17_koreksi_manset_kurang'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-rose-300">{{ number_format($grandTotal['c18_kdp_kurang'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-rose-300 bg-rose-950/40">
                        {{ number_format($grandTotal['total_pengurangan'] ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="p-3 text-right text-emerald-300 bg-slate-950">
                        {{ number_format($grandTotal['saldo_akhir'] ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

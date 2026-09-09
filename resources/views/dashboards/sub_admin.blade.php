<x-layout title="Dashboard Sub Admin - SIMAT-RK">
    @section('page-title', 'Dashboard Sub Admin')
    @section('breadcrumb', 'Beranda / Sub Admin')

    @php
        $unitNama = $unit->nama ?? 'Unit Ruangan';
        $unitKode = $unit->kode_unit ?? ('UNIT-' . str_pad($unit->id ?? 1, 3, '0', STR_PAD_LEFT));
        $unitTipe = $unit->tipe ?? 'Unit Pelayanan Medis / Operasional';
        $unitKepala = $unit->kepala ?? Auth::user()->name;
        $unitNip = $unit->nip ?? Auth::user()->nip ?? '-';
        $unitTotalAset = $totalAsetCount ?? 0;
        $unitTotalNilai = $totalNilaiFormatted ?? 'Rp 0';
    @endphp

    <div x-data="{
        // Daftar Aset Ruangan yang Perlu Perhatian / Pemeliharaan dari Database Backend
        attentionAssets: {{ Js::from($attentionAssets ?? []) }},
    }" x-cloak>

        <!-- 1. HEADER & IDENTITAS UNIT TERDAFTAR -->
        <div class="bg-gradient-to-r from-emerald-600/15 via-teal-950/40 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-8 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-0 right-1/4 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                <!-- Info Sub Admin & Unit -->
                <div class="space-y-3 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>SUB ADMIN · PENANGGUNG JAWAB RUANGAN</span>
                        </span>
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-700 text-xs font-mono font-semibold">
                            <span>{{ $unitKode }}</span>
                        </span>
                    </div>

                    <div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight flex flex-wrap items-center gap-3">
                            <span>🏥 {{ $unitNama }}</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                            Penanggung Jawab: <span class="font-bold text-white">{{ $unitKepala }}</span> 
                            <span class="text-slate-400">(NIP. {{ $unitNip }})</span> · 
                            <span class="text-emerald-400 font-semibold">{{ $unitTipe }}</span>
                        </p>
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pantau inventaris fisik ruangan Anda, visualisasi grafik nilai dan kondisi aset, telusuri katalog barang ASTAP, serta cetak Lembar Kartu Inventaris Ruangan (KIR) resmi.
                    </p>
                </div>

                <!-- Action Buttons Khusus Sub Admin -->
                <div class="flex flex-wrap sm:flex-nowrap gap-2.5 shrink-0 w-full sm:w-auto">
                    <a href="{{ route('astap.index') }}" 
                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <span>Katalog ASTAP</span>
                    </a>

                    <a href="{{ route('kir.index') }}" 
                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition-all flex items-center justify-center space-x-2 shadow-lg shadow-emerald-500/20">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Lembar KIR Ruangan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. RINGKASAN METRIK KHUSUS RUANGAN (4 STATS CARDS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- 1. Total Aset Ruangan -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Aset di Ruangan Ini</span>
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $unitTotalAset }}</p>
                    <span class="text-xs font-bold text-emerald-400">Unit Barang</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                    <span>Tercatat Resmi Dokumen KIR</span>
                </p>
            </div>

            <!-- 2. Valuasi Nilai Aset Ruangan -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-cyan-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Nilai Aset Ruangan</span>
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-1">
                    <p class="text-xl sm:text-2xl font-black text-white">{{ $unitTotalNilai }}</p>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    Estimasi Nilai Buku Inventaris Unit
                </p>
            </div>

            <!-- 3. Kondisi Aset Siap Pakai (Baik) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-teal-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Kondisi Siap Pakai</span>
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $kondisiBaik ?? 0 }}</p>
                    <span class="text-xs font-bold text-emerald-400">Unit Baik</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    {{ $unitTotalAset > 0 ? round((($kondisiBaik ?? 0) / $unitTotalAset) * 100, 1) : 0 }}% dari total aset ruangan
                </p>
            </div>

            <!-- 4. Aset Perlu Perhatian / Servis -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-amber-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Perlu Perhatian</span>
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $totalRusak ?? 0 }}</p>
                    <span class="text-xs font-bold text-amber-400">Unit Servis/Rusak</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    {{ $kondisiRusakRingan ?? 0 }} Rusak Ringan · {{ $kondisiRusakBerat ?? 0 }} Rusak Berat
                </p>
            </div>
        </div>

        <!-- 3. GRAFIK NILAI ASET RUANGAN & GRAFIK KONDISI ASET RUANGAN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- GRAFIK 1 (2 Kolom): GRAFIK NILAI ASET RUANGAN (VALUASI & KUANTITAS) -->
            <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                        <div>
                            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold mb-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>GRAFIK NILAI ASET RUANGAN</span>
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-white">Pertumbuhan Nilai & Kuantitas Aset {{ $unitNama }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Visualisasi akumulasi valuasi harga dan jumlah unit barang per tahun pengadaan di ruangan ini</p>
                        </div>

                        <!-- Mode Selector Toggle Pills -->
                        <div class="inline-flex p-1 bg-slate-950 border border-slate-800 rounded-2xl shrink-0 text-xs font-semibold">
                            <button id="btnKumulatifSub" onclick="switchSubAdminChartMode('kumulatif')" class="px-3 py-1.5 rounded-xl bg-emerald-500 text-slate-950 font-bold transition-all shadow-md">
                                📈 Akumulasi
                            </button>
                            <button id="btnPerTahunSub" onclick="switchSubAdminChartMode('pertahun')" class="px-3 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all">
                                📊 Per Tahun
                            </button>
                        </div>
                    </div>

                    <!-- Canvas Chart Nilai Aset -->
                    <div class="relative w-full h-[280px] sm:h-[310px] p-3 bg-slate-950/50 rounded-2xl border border-slate-800/80">
                        <canvas id="roomAstapGrowthChart"></canvas>
                    </div>
                </div>

                <!-- Footer Keterangan Chart Nilai -->
                <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Total Valuasi: <strong class="text-white">{{ $unitTotalNilai }}</strong></span>
                    </div>
                    <span class="text-[11px] text-slate-500">Unit: {{ $unitNama }}</span>
                </div>
            </div>

            <!-- GRAFIK 2 (1 Kolom): GRAFIK KONDISI ASET RUANGAN -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 border border-teal-500/30 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                            <span>KONDISI FISIK ASET</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-400">{{ $unitTotalAset }} Unit Total</span>
                    </div>
                    <h3 class="text-base font-extrabold text-white">Kondisi Aset Ruangan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Proporsi fisik kesiapan inventaris ruangan</p>

                    <!-- Chart Canvas Container -->
                    <div class="relative w-full h-[200px] sm:h-[220px] my-3 flex items-center justify-center">
                        <canvas id="roomKondisiChart"></canvas>
                    </div>
                </div>

                <!-- Summary Chips Kondisi -->
                <div class="grid grid-cols-4 gap-1.5 pt-3 border-t border-slate-800/80 text-center">
                    <div class="p-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                        <div class="text-[9px] text-emerald-400 font-bold uppercase">Baik</div>
                        <div class="text-xs sm:text-sm font-black text-white font-mono">{{ $kondisiBaik ?? 0 }}</div>
                    </div>
                    <div class="p-1.5 rounded-xl bg-amber-500/10 border border-amber-500/20">
                        <div class="text-[9px] text-amber-400 font-bold uppercase">K. Baik</div>
                        <div class="text-xs sm:text-sm font-black text-white font-mono">{{ $kondisiKurangBaik ?? 0 }}</div>
                    </div>
                    <div class="p-1.5 rounded-xl bg-orange-500/10 border border-orange-500/20">
                        <div class="text-[9px] text-orange-400 font-bold uppercase">R. Ringan</div>
                        <div class="text-xs sm:text-sm font-black text-white font-mono">{{ $kondisiRusakRingan ?? 0 }}</div>
                    </div>
                    <div class="p-1.5 rounded-xl bg-rose-500/10 border border-rose-500/20">
                        <div class="text-[9px] text-rose-400 font-bold uppercase">R. Berat</div>
                        <div class="text-xs sm:text-sm font-black text-white font-mono">{{ $kondisiRusakBerat ?? 0 }}</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- 4. MODUL UTAMA: KATALOG & LEMBAR KIR (LEMBAR KIR DITARUH DI BAWAH KATALOG) + ASET PERLU PERHATIAN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- KOLOM KIRI (2/3): KATALOG DATA ASTAP & LEMBAR KIR RUANGAN DI BAWAHNYA -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- CARD 1: KATALOG DATA ASTAP -->
                <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 border border-teal-500/30 rounded-3xl p-6 shadow-xl relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 text-xl">
                                📦
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-mono font-bold text-teal-400 uppercase tracking-wider">Katalog Inventaris Induk</span>
                                </div>
                                <h3 class="text-lg font-extrabold text-white">Katalog Data ASTAP</h3>
                            </div>
                        </div>

                        <a href="{{ route('astap.index') }}" 
                            class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center justify-center space-x-2 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Buka Katalog ASTAP &rarr;</span>
                        </a>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Telusuri seluruh katalog master inventaris aset tetap RSUD Dr. H. Koesnandi. Cari spesifikasi barang, nomor inventaris 108, kode register NIBAR, riwayat pengadaan, dan status ketersediaan barang inventaris.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3 flex items-center space-x-2.5">
                            <span class="text-emerald-400 font-bold text-sm">🔍</span>
                            <div>
                                <span class="text-slate-400 text-[11px] block">Pencarian Cepat</span>
                                <span class="text-white font-semibold text-xs">Nama & Kode Barang</span>
                            </div>
                        </div>
                        <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3 flex items-center space-x-2.5">
                            <span class="text-teal-400 font-bold text-sm">🏷️</span>
                            <div>
                                <span class="text-slate-400 text-[11px] block">Standar Kode 108</span>
                                <span class="text-white font-semibold text-xs">Permendagri No. 108</span>
                            </div>
                        </div>
                        <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3 flex items-center space-x-2.5">
                            <span class="text-cyan-400 font-bold text-sm">📱</span>
                            <div>
                                <span class="text-slate-400 text-[11px] block">Label Barcode QR</span>
                                <span class="text-white font-semibold text-xs">Scan Fisik Terintegrasi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: LEMBAR KIR RUANGAN (DITARUH TEPAT DI BAWAH KATALOG) -->
                <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 border border-emerald-500/30 rounded-3xl p-6 shadow-xl relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xl">
                                📋
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-wider">Kartu Inventaris Ruangan</span>
                                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-full">Tersinkronisasi</span>
                                </div>
                                <h3 class="text-lg font-extrabold text-white">Lembar KIR Ruangan {{ $unitNama }}</h3>
                            </div>
                        </div>

                        <a href="{{ route('kir.index') }}" 
                            class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center space-x-2 shrink-0">
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Buka & Cetak Dokumen KIR</span>
                        </a>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Daftar lengkap inventaris fisik yang ditempatkan resmi di <strong class="text-white">{{ $unitNama }}</strong>. Anda dapat mencetak lembar resmi KIR ber-barcode standar rumah sakit untuk ditempel pada pintu ruangan atau dinding inventaris.
                    </p>

                    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-4 space-y-2.5 text-xs">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 text-slate-300">
                            <span class="text-slate-500">Unit Ruangan:</span>
                            <span class="font-bold text-white">{{ $unitNama }} ({{ $unitKode }})</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 text-slate-300 border-t border-slate-800/80 pt-2">
                            <span class="text-slate-500">Total Item Terpasang:</span>
                            <span class="font-bold text-emerald-400">{{ $unitTotalAset }} Unit Barang (Valuasi: {{ $unitTotalNilai }})</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 text-slate-300 border-t border-slate-800/80 pt-2">
                            <span class="text-slate-500">Penanggung Jawab Ruangan:</span>
                            <span class="font-semibold text-slate-200">{{ $unitKepala }} <span class="text-slate-400 font-mono">(NIP. {{ $unitNip }})</span></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- KOLOM KANAN (1/3): CARD ASET PERLU PERHATIAN (DENGAN TAMPILAN YANG SUDAH DIPERBAIKI) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">🛠️</span>
                            <h4 class="text-sm font-extrabold text-white">Aset Perlu Perhatian</h4>
                        </div>
                        <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 border border-amber-500/30 px-2.5 py-0.5 rounded-full"
                              x-text="attentionAssets.length + ' Item'"></span>
                    </div>

                    <p class="text-xs text-slate-400 mb-3">
                        Barang di ruangan Anda yang mengalami kendala atau membutuhkan servis berkala:
                    </p>

                    <!-- List Aset Rusak / Servis dengan perbaikan tampilan nomor NIBAR panjang & lokasi -->
                    <div class="space-y-3">
                        <template x-for="item in attentionAssets.slice(0, 5)" :key="item.id">
                            <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-xs hover:border-slate-700 transition-all">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="font-bold text-white text-xs leading-snug" x-text="item.nama"></p>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0"
                                        :class="item.status === 'Rusak Ringan' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : (item.status === 'Rusak Berat' || item.status === 'Rusak' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30')"
                                        x-text="item.status">
                                    </span>
                                </div>
                                
                                <p class="text-[11px] text-slate-400 mt-1.5 leading-relaxed" x-text="item.catatan"></p>
                                
                                <!-- Container Bawah: NIBAR dan Lokasi Dipisah Secara Rapi & Bebas Tabrakan -->
                                <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[10px]">
                                    <!-- NIBAR dengan tooltip & truncate -->
                                    <div class="flex items-center space-x-1.5 min-w-0 max-w-full">
                                        <span class="text-slate-500 shrink-0 font-medium">NIBAR:</span>
                                        <span class="font-mono text-emerald-400 font-semibold truncate block" 
                                              :title="item.kode" 
                                              x-text="item.kode"></span>
                                    </div>

                                    <!-- Lokasi Ruangan -->
                                    <div class="flex items-center space-x-1 shrink-0 text-slate-400">
                                        <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="truncate max-w-[140px]" :title="item.lokasi" x-text="item.lokasi"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="attentionAssets.length === 0">
                            <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 text-center text-xs text-slate-400">
                                <span class="text-2xl block mb-1.5">✅</span>
                                <p class="text-emerald-400 font-bold">Seluruh Aset Berstatus Baik</p>
                                <p class="text-[11px] text-slate-500 mt-1">Tidak ada aset rusak yang tercatat di ruangan ini.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <a href="{{ route('pemeliharaan.index') }}" 
                    class="mt-4 block text-center w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all shadow-sm">
                    Lihat Seluruh Log Pemeliharaan &rarr;
                </a>
            </div>

        </div>

    </div>

    <!-- Script Inisialisasi Chart.js untuk Dashboard Sub Admin -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
    function initSubAdminCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(initSubAdminCharts, 100);
            return;
        }

        // 1. Data Grafik Nilai Aset Ruangan
        const chartYears = {{ Js::from($chartYears ?? []) }};
        const hargaKumulatif = {{ Js::from($chartRoomKumulatifHargaJuta ?? []) }};
        const volKumulatif = {{ Js::from($chartRoomKumulatifVolume ?? []) }};
        const hargaPerTahun = {{ Js::from($chartRoomHargaJuta ?? []) }};
        const volPerTahun = {{ Js::from($chartRoomVolume ?? []) }};
        const rawHargaKumulatif = {{ Js::from(array_map(fn($v) => (float)$v, $chartRoomHarga ?? [])) }};

        const growthCtx = document.getElementById('roomAstapGrowthChart');
        if (growthCtx) {
            window.roomAstapChart = new Chart(growthCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartYears,
                    datasets: [
                        {
                            label: 'Akumulasi Valuasi (Rp Juta)',
                            data: hargaKumulatif,
                            borderColor: '#10b981', // Emerald
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointBackgroundColor: '#10b981',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            yAxisID: 'y'
                        },
                        {
                            type: 'bar',
                            label: 'Akumulasi Kuantitas (Unit)',
                            data: volKumulatif,
                            backgroundColor: 'rgba(20, 184, 166, 0.35)', // Teal
                            borderColor: '#14b8a6',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            maxBarThickness: 32,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#94a3b8',
                                font: { size: 11, weight: '600' },
                                usePointStyle: true,
                                padding: 12
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw || 0;
                                    if (context.dataset.yAxisID === 'y') {
                                        return ` Valuasi: Rp ${val.toLocaleString('id-ID')} Juta`;
                                    }
                                    return ` Kuantitas: ${val} Unit Barang`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(51, 65, 85, 0.3)' },
                            ticks: { color: '#94a3b8', font: { size: 10 } }
                        },
                        y: {
                            type: 'linear',
                            position: 'left',
                            grid: { color: 'rgba(51, 65, 85, 0.3)' },
                            ticks: {
                                color: '#10b981',
                                font: { size: 10 },
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID') + ' Jt';
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: {
                                color: '#14b8a6',
                                font: { size: 10 },
                                callback: function(value) {
                                    return value + ' Unit';
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Inisialisasi Grafik Kondisi Aset Ruangan (Doughnut Chart)
        const kondisiCtx = document.getElementById('roomKondisiChart');
        if (kondisiCtx) {
            const kondisiBaik = {{ (int)($kondisiBaik ?? 0) }};
            const kondisiKurangBaik = {{ (int)($kondisiKurangBaik ?? 0) }};
            const kondisiRusakRingan = {{ (int)($kondisiRusakRingan ?? 0) }};
            const kondisiRusakBerat = {{ (int)($kondisiRusakBerat ?? 0) }};

            new Chart(kondisiCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'],
                    datasets: [{
                        data: [kondisiBaik, kondisiKurangBaik, kondisiRusakRingan, kondisiRusakBerat],
                        backgroundColor: [
                            '#10b981', // Emerald - Baik
                            '#f59e0b', // Amber - Kurang Baik
                            '#f97316', // Orange - Rusak Ringan
                            '#ef4444', // Rose - Rusak Berat
                        ],
                        borderColor: '#020617',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                font: { size: 10, weight: 'bold' },
                                usePointStyle: true,
                                padding: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const val = context.raw || 0;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${val} Unit (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        window.switchSubAdminChartMode = function (mode) {
            const btnKumulatif = document.getElementById('btnKumulatifSub');
            const btnPerTahun = document.getElementById('btnPerTahunSub');
            if (!btnKumulatif || !btnPerTahun || !window.roomAstapChart) return;

            if (mode === 'kumulatif') {
                btnKumulatif.className = "px-3 py-1.5 rounded-xl bg-emerald-500 text-slate-950 font-bold transition-all shadow-md";
                btnPerTahun.className = "px-3 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all";

                window.roomAstapChart.data.datasets[0].label = 'Akumulasi Valuasi (Rp Juta)';
                window.roomAstapChart.data.datasets[0].data = hargaKumulatif;
                window.roomAstapChart.data.datasets[1].label = 'Akumulasi Kuantitas (Unit)';
                window.roomAstapChart.data.datasets[1].data = volKumulatif;
            } else {
                btnPerTahun.className = "px-3 py-1.5 rounded-xl bg-teal-500 text-slate-950 font-bold transition-all shadow-md";
                btnKumulatif.className = "px-3 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all";

                window.roomAstapChart.data.datasets[0].label = 'Pengadaan Valuasi (Rp Juta/Thn)';
                window.roomAstapChart.data.datasets[0].data = hargaPerTahun;
                window.roomAstapChart.data.datasets[1].label = 'Pengadaan Kuantitas (Unit/Thn)';
                window.roomAstapChart.data.datasets[1].data = volPerTahun;
            }
            window.roomAstapChart.update();
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSubAdminCharts);
    } else {
        initSubAdminCharts();
    }
    </script>
</x-layout>
